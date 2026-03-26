// screens/ScanScreen.js
// Requiere: expo-camera >= SDK 50
import React, { useState, useRef, useCallback } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  Alert, TextInput, Modal, Vibration,
  KeyboardAvoidingView, Platform, ActivityIndicator, ScrollView,
} from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { MaterialIcons } from '@expo/vector-icons';

const API_BASE = 'http://192.168.100.38:8000';

// Solo códigos de barras lineales — sin QR
const BARCODE_TYPES = ['ean13', 'ean8', 'code128', 'code39', 'upc_a', 'upc_e', 'itf14', 'codabar'];

// ─────────────────────────────────────────────────────────────────────────────
// Modal entrada manual — FUERA del componente para evitar re-mounts
// ─────────────────────────────────────────────────────────────────────────────
const ManualEntryModal = ({ visible, manualCode, onChangeText, onSubmit, onClose, inputRef }) => (
  <Modal animationType="slide" transparent visible={visible} onRequestClose={onClose}>
    <KeyboardAvoidingView
      style={styles.modalContainer}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <View style={styles.modalContent}>
        <View style={styles.modalHeader}>
          <Text style={styles.modalTitle}>Ingresar Código</Text>
          <TouchableOpacity onPress={onClose}>
            <MaterialIcons name="close" size={24} color="#7f8c8d" />
          </TouchableOpacity>
        </View>

        <Text style={styles.modalLabel}>Código de Barras / SKU</Text>
        <TextInput
          ref={inputRef}
          style={styles.modalInput}
          placeholder="Ej: LPT-HP-001"
          value={manualCode}
          onChangeText={onChangeText}
          autoCapitalize="characters"
          autoCorrect={false}
          returnKeyType="done"
          onSubmitEditing={onSubmit}
          blurOnSubmit={false}
        />

        <View style={styles.modalButtons}>
          <TouchableOpacity style={[styles.modalButton, styles.cancelButton]} onPress={onClose}>
            <Text style={styles.cancelButtonText}>Cancelar</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.modalButton, styles.confirmButton]} onPress={onSubmit}>
            <MaterialIcons name="add-circle" size={18} color="white" />
            <Text style={styles.confirmButtonText}>Agregar</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.modalFooter}>
          <Text style={styles.modalFooterText}>
            Formatos: EAN-13, EAN-8, Code 128, Code 39, UPC
          </Text>
        </View>
      </View>
    </KeyboardAvoidingView>
  </Modal>
);

// ─────────────────────────────────────────────────────────────────────────────
// Modal formulario para producto NUEVO — solo aparece si el SKU no existe
// ─────────────────────────────────────────────────────────────────────────────
const NewProductModal = ({ visible, onClose, onSave, sku, form, onChangeForm, isSaving }) => (
  <Modal animationType="slide" transparent visible={visible} onRequestClose={onClose}>
    <KeyboardAvoidingView
      style={styles.modalContainer}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView
        contentContainerStyle={styles.saveModalScroll}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.saveModalContent}>

          <View style={styles.modalHeader}>
            <View style={{ flex: 1 }}>
              <Text style={styles.modalTitle}>Producto Nuevo</Text>
              <Text style={styles.skuTag}>SKU: {sku}</Text>
            </View>
            <TouchableOpacity onPress={onClose}>
              <MaterialIcons name="close" size={24} color="#7f8c8d" />
            </TouchableOpacity>
          </View>

          <View style={styles.infoBadgeNew}>
            <MaterialIcons name="info-outline" size={16} color="#2563eb" />
            <Text style={styles.infoBadgeText}>
              Este código no existe en inventario. Completa los datos para agregarlo.
            </Text>
          </View>

          <Text style={styles.fieldLabel}>Nombre del producto *</Text>
          <View style={styles.fieldRow}>
            <MaterialIcons name="label" size={18} color="#7f8c8d" style={styles.fieldIcon} />
            <TextInput
              style={styles.fieldInput}
              placeholder="Ej: Laptop HP EliteBook"
              value={form.nombre}
              onChangeText={v => onChangeForm('nombre', v)}
            />
          </View>

          <Text style={styles.fieldLabel}>Ubicación *</Text>
          <View style={styles.fieldRow}>
            <MaterialIcons name="location-on" size={18} color="#7f8c8d" style={styles.fieldIcon} />
            <TextInput
              style={styles.fieldInput}
              placeholder="Ej: A-12-03"
              value={form.ubicacion}
              onChangeText={v => onChangeForm('ubicacion', v)}
              autoCapitalize="characters"
            />
          </View>

          <Text style={styles.fieldLabel}>Categoría</Text>
          <View style={styles.fieldRow}>
            <MaterialIcons name="category" size={18} color="#7f8c8d" style={styles.fieldIcon} />
            <TextInput
              style={styles.fieldInput}
              placeholder="Ej: Electrónica"
              value={form.categoria}
              onChangeText={v => onChangeForm('categoria', v)}
            />
          </View>

          <Text style={styles.fieldHint}>
            Se creará con cantidad 1. Puedes ajustarla desde el inventario.
          </Text>

          <View style={styles.saveButtonRow}>
            <TouchableOpacity
              style={[styles.saveModalButton, styles.cancelButton]}
              onPress={onClose}
              disabled={isSaving}
            >
              <Text style={styles.cancelButtonText}>Cancelar</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.saveModalButton, styles.saveButton, isSaving && styles.disabledBtn]}
              onPress={onSave}
              disabled={isSaving}
            >
              {isSaving
                ? <ActivityIndicator color="white" size="small" />
                : <>
                    <MaterialIcons name="save" size={18} color="white" />
                    <Text style={styles.saveButtonText}>Guardar</Text>
                  </>
              }
            </TouchableOpacity>
          </View>

        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  </Modal>
);

// ─────────────────────────────────────────────────────────────────────────────
// Componente principal
// ─────────────────────────────────────────────────────────────────────────────
const ScanScreen = ({ navigation }) => {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned]           = useState(false);
  const [flashEnabled, setFlashEnabled] = useState(false);
  const [facing, setFacing]             = useState('back');

  // Modal entrada manual
  const [manualModalVisible, setManualModalVisible] = useState(false);
  const [manualCode, setManualCode]                 = useState('');
  const inputRef = useRef(null);

  // Modal nuevo producto
  const [newProductModal, setNewProductModal] = useState(false);
  const [currentSku, setCurrentSku]           = useState('');
  const [isSaving, setIsSaving]               = useState(false);
  const [isProcessing, setIsProcessing]       = useState(false);
  const [form, setForm] = useState({ nombre: '', ubicacion: '', categoria: '' });

  // Toast de confirmación
  const [toast, setToast]   = useState(null);
  const toastTimerRef       = useRef(null);

  // ── Helpers ───────────────────────────────────────────────────────────────
  const updateForm = useCallback((key, value) => {
    setForm(prev => ({ ...prev, [key]: value }));
  }, []);

  const showToast = useCallback((msg, type = 'success') => {
    if (toastTimerRef.current) clearTimeout(toastTimerRef.current);
    setToast({ msg, type });
    toastTimerRef.current = setTimeout(() => setToast(null), 2800);
  }, []);

  const abrirManualModal = useCallback(() => {
    setManualCode('');
    setManualModalVisible(true);
    setTimeout(() => inputRef.current?.focus(), 300);
  }, []);

  const cerrarManualModal = useCallback(() => {
    setManualModalVisible(false);
    setManualCode('');
  }, []);

  // ── Lógica principal: procesar código ────────────────────────────────────
  const procesarCodigo = useCallback(async (sku) => {
    if (isProcessing) return;
    setIsProcessing(true);

    try {
      const res  = await fetch(`${API_BASE}/materiales`);
      const data = await res.json();
      const found = data.find(item => item.sku?.toUpperCase() === sku.toUpperCase());

      if (found) {
        // Producto existente → +1 automático
        const nuevaCantidad = (found.cantidad || 0) + 1;
        const patch = await fetch(`${API_BASE}/materiales/${found.id}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ cantidad: nuevaCantidad }),
        });

        if (patch.ok) {
          showToast(`✅ ${found.nombre}  ${found.cantidad} → ${nuevaCantidad} unid.`, 'success');
        } else {
          showToast('❌ Error al actualizar', 'error');
        }
        setScanned(false);
        setIsProcessing(false);
      } else {
        // Producto nuevo → abrir formulario
        setCurrentSku(sku);
        setForm({ nombre: '', ubicacion: '', categoria: '' });
        setNewProductModal(true);
        setIsProcessing(false);
      }
    } catch {
      showToast('❌ Sin conexión al servidor', 'error');
      setScanned(false);
      setIsProcessing(false);
    }
  }, [isProcessing, showToast]);

  // ── Guardar producto nuevo ────────────────────────────────────────────────
  const handleSaveNew = useCallback(async () => {
    if (!form.nombre.trim())    { Alert.alert('Error', 'Ingresa el nombre del producto'); return; }
    if (!form.ubicacion.trim()) { Alert.alert('Error', 'Ingresa la ubicación'); return; }

    setIsSaving(true);
    try {
      const res = await fetch(`${API_BASE}/materiales`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          sku:                 currentSku,
          nombre:              form.nombre.trim(),
          cantidad:            1,
          ubicacion:           form.ubicacion.trim().toUpperCase(),
          categoria:           form.categoria.trim() || 'General',
          stockMinimo:         10,
          stockMaximo:         100,
          ultimaActualizacion: new Date().toLocaleDateString('es-MX'),
        }),
      });

      if (res.ok) {
        setNewProductModal(false);
        setScanned(false);
        showToast(`✅ "${form.nombre.trim()}" agregado al inventario`, 'success');
      } else {
        const err = await res.json().catch(() => ({}));
        Alert.alert('Error del servidor', err?.detail || `Error ${res.status}`);
      }
    } catch {
      Alert.alert('Error de conexión', 'No se pudo conectar con el servidor.');
    } finally {
      setIsSaving(false);
    }
  }, [form, currentSku, showToast]);

  // ── Cámara: código escaneado ──────────────────────────────────────────────
  const handleBarCodeScanned = useCallback(({ data }) => {
    if (scanned || isProcessing) return;
    setScanned(true);
    Vibration.vibrate(120);
    procesarCodigo(data.trim().toUpperCase());
  }, [scanned, isProcessing, procesarCodigo]);

  // ── Entrada manual ────────────────────────────────────────────────────────
  const handleManualEntry = useCallback(() => {
    if (!manualCode.trim()) { Alert.alert('Error', 'Ingresa un código válido'); return; }
    const code = manualCode.trim().toUpperCase();
    cerrarManualModal();
    setTimeout(() => procesarCodigo(code), 350);
  }, [manualCode, cerrarManualModal, procesarCodigo]);

  // ── Simulación ────────────────────────────────────────────────────────────
  const handleSimulate = () => {
    Alert.alert('Modo Simulación', 'Selecciona un código para simular', [
      { text: 'Cancelar', style: 'cancel' },
      { text: 'EAN-13: Laptop HP (existente)',    onPress: () => handleBarCodeScanned({ data: 'LPT-HP-001' }) },
      { text: 'EAN-13: Monitor Dell (existente)', onPress: () => handleBarCodeScanned({ data: 'MON-DL-002' }) },
      { text: 'Code128: Nuevo producto',          onPress: () => handleBarCodeScanned({ data: 'TEST-999' }) },
    ]);
  };

  // ── Permisos ──────────────────────────────────────────────────────────────
  if (!permission) {
    return (
      <View style={styles.centeredContainer}>
        <ActivityIndicator size="large" color="#3498db" />
        <Text style={styles.infoText}>Verificando permisos de cámara...</Text>
      </View>
    );
  }

  if (!permission.granted) {
    return (
      <View style={styles.centeredContainer}>
        <MaterialIcons name="no-photography" size={60} color="#e74c3c" />
        <Text style={styles.noPermissionText}>Sin acceso a la cámara</Text>
        <Text style={styles.noPermissionSubtext}>
          Se necesita acceso a la cámara para escanear códigos
        </Text>
        <TouchableOpacity style={styles.permissionButton} onPress={requestPermission}>
          <Text style={styles.permissionButtonText}>Conceder Permiso</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.permissionButton, { backgroundColor: '#7f8c8d', marginTop: 10 }]}
          onPress={abrirManualModal}
        >
          <Text style={styles.permissionButtonText}>Entrada Manual</Text>
        </TouchableOpacity>
        <ManualEntryModal
          visible={manualModalVisible} manualCode={manualCode}
          onChangeText={setManualCode} onSubmit={handleManualEntry}
          onClose={cerrarManualModal} inputRef={inputRef}
        />
        <NewProductModal
          visible={newProductModal}
          onClose={() => { setNewProductModal(false); setScanned(false); }}
          onSave={handleSaveNew} sku={currentSku}
          form={form} onChangeForm={updateForm} isSaving={isSaving}
        />
      </View>
    );
  }

  // ── Vista principal con cámara ────────────────────────────────────────────
  return (
    <View style={styles.container}>

      {isProcessing && (
        <View style={styles.processingOverlay}>
          <ActivityIndicator size="large" color="white" />
          <Text style={styles.processingText}>Procesando...</Text>
        </View>
      )}

      {toast && (
        <View style={[styles.toast, toast.type === 'error' ? styles.toastError : styles.toastSuccess]}>
          <Text style={styles.toastText}>{toast.msg}</Text>
        </View>
      )}

      <CameraView
        style={StyleSheet.absoluteFillObject}
        facing={facing}
        enableTorch={flashEnabled}
        barcodeScannerSettings={{ barcodeTypes: BARCODE_TYPES }}
        onBarcodeScanned={scanned || isProcessing ? undefined : handleBarCodeScanned}
      >
        <View style={styles.overlay}>

          <View style={styles.header}>
            <TouchableOpacity style={styles.headerButton} onPress={() => navigation.goBack()}>
              <MaterialIcons name="arrow-back" size={24} color="white" />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Escanear Código</Text>
            <View style={styles.headerControls}>
              <TouchableOpacity style={styles.headerButton} onPress={() => setFlashEnabled(v => !v)}>
                <MaterialIcons name={flashEnabled ? 'flash-on' : 'flash-off'} size={24} color="white" />
              </TouchableOpacity>
              <TouchableOpacity style={styles.headerButton} onPress={() => setFacing(f => f === 'back' ? 'front' : 'back')}>
                <MaterialIcons name="flip-camera-android" size={24} color="white" />
              </TouchableOpacity>
            </View>
          </View>

          <View style={styles.scanArea}>
            <View style={styles.scanFrame}>
              <View style={[styles.corner, styles.cornerTL]} />
              <View style={[styles.corner, styles.cornerTR]} />
              <View style={[styles.corner, styles.cornerBL]} />
              <View style={[styles.corner, styles.cornerBR]} />
              <View style={styles.scanLine} />
            </View>
          </View>

          <View style={styles.instructionContainer}>
            <Text style={styles.instructionText}>Apunta al código de barras</Text>
            <Text style={styles.instructionSubtext}>
              Si el producto existe se suma +1 automáticamente
            </Text>
          </View>

          <View style={styles.buttonContainer}>
            <TouchableOpacity style={styles.actionButton} onPress={handleSimulate}>
              <MaterialIcons name="science" size={20} color="white" />
              <Text style={styles.actionButtonText}>Simular</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.actionButton, styles.manualButton]} onPress={abrirManualModal}>
              <MaterialIcons name="keyboard" size={20} color="white" />
              <Text style={styles.actionButtonText}>Manual</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.actionButton, styles.inventoryButton]} onPress={() => navigation.navigate('Inventory')}>
              <MaterialIcons name="list" size={20} color="white" />
              <Text style={styles.actionButtonText}>Inventario</Text>
            </TouchableOpacity>
          </View>

        </View>
      </CameraView>

      <ManualEntryModal
        visible={manualModalVisible} manualCode={manualCode}
        onChangeText={setManualCode} onSubmit={handleManualEntry}
        onClose={cerrarManualModal} inputRef={inputRef}
      />
      <NewProductModal
        visible={newProductModal}
        onClose={() => { setNewProductModal(false); setScanned(false); }}
        onSave={handleSaveNew} sku={currentSku}
        form={form} onChangeForm={updateForm} isSaving={isSaving}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },

  processingOverlay: {
    position: 'absolute', zIndex: 99,
    top: 0, left: 0, right: 0, bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.6)',
    justifyContent: 'center', alignItems: 'center',
  },
  processingText: { color: 'white', fontSize: 16, marginTop: 12, fontWeight: '600' },

  toast: {
    position: 'absolute', top: 60, left: 20, right: 20, zIndex: 100,
    borderRadius: 12, paddingVertical: 14, paddingHorizontal: 18, elevation: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.3, shadowRadius: 6,
  },
  toastSuccess: { backgroundColor: '#1e293b' },
  toastError:   { backgroundColor: '#e74c3c' },
  toastText: { color: 'white', fontSize: 15, fontWeight: '600', textAlign: 'center' },

  centeredContainer: {
    flex: 1, justifyContent: 'center', alignItems: 'center',
    backgroundColor: '#f5f5f5', padding: 30,
  },
  infoText: { fontSize: 16, color: '#7f8c8d', marginTop: 12 },
  noPermissionText: { fontSize: 18, fontWeight: 'bold', color: '#2c3e50', marginTop: 15 },
  noPermissionSubtext: {
    fontSize: 14, color: '#7f8c8d', textAlign: 'center', marginTop: 8, marginBottom: 20,
  },
  permissionButton: {
    backgroundColor: '#3498db', paddingVertical: 14, paddingHorizontal: 30, borderRadius: 10,
  },
  permissionButtonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },

  overlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)', justifyContent: 'space-between' },

  header: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', padding: 20, paddingTop: 50,
  },
  headerTitle: { color: 'white', fontSize: 17, fontWeight: '700' },
  headerButton: {
    width: 40, height: 40, borderRadius: 20,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center', alignItems: 'center',
  },
  headerControls: { flexDirection: 'row', gap: 8 },

  scanArea:  { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scanFrame: { width: 280, height: 160, position: 'relative', justifyContent: 'center' },
  corner:    { position: 'absolute', width: 28, height: 28, borderColor: '#3498db' },
  cornerTL:  { top: 0, left: 0, borderTopWidth: 3, borderLeftWidth: 3 },
  cornerTR:  { top: 0, right: 0, borderTopWidth: 3, borderRightWidth: 3 },
  cornerBL:  { bottom: 0, left: 0, borderBottomWidth: 3, borderLeftWidth: 3 },
  cornerBR:  { bottom: 0, right: 0, borderBottomWidth: 3, borderRightWidth: 3 },
  scanLine: {
    position: 'absolute', left: 10, right: 10,
    height: 2, backgroundColor: 'rgba(52,152,219,0.8)',
  },

  instructionContainer: { alignItems: 'center', marginBottom: 16, paddingHorizontal: 24 },
  instructionText:    { color: 'white', fontSize: 17, fontWeight: 'bold', textAlign: 'center' },
  instructionSubtext: { color: 'rgba(255,255,255,0.75)', fontSize: 13, marginTop: 6, textAlign: 'center' },

  buttonContainer: {
    flexDirection: 'row', justifyContent: 'space-around',
    marginBottom: 40, paddingHorizontal: 16,
  },
  actionButton: {
    backgroundColor: 'rgba(52,152,219,0.85)', paddingVertical: 11,
    paddingHorizontal: 14, borderRadius: 22,
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 5,
  },
  manualButton:     { backgroundColor: 'rgba(46,204,113,0.85)' },
  inventoryButton:  { backgroundColor: 'rgba(155,89,182,0.85)' },
  actionButtonText: { color: 'white', fontSize: 13, fontWeight: '600' },

  modalContainer: {
    flex: 1, justifyContent: 'center', alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.55)',
  },
  modalContent: {
    backgroundColor: 'white', borderRadius: 16,
    padding: 20, width: '85%', maxWidth: 400,
  },
  modalHeader: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'flex-start', marginBottom: 16,
  },
  modalTitle:  { fontSize: 20, fontWeight: 'bold', color: '#2c3e50' },
  modalLabel:  { fontSize: 14, color: '#7f8c8d', marginBottom: 6 },
  modalInput: {
    borderWidth: 1.5, borderColor: '#bdc3c7',
    borderRadius: 10, padding: 14, fontSize: 16, marginBottom: 20,
  },
  modalButtons: { flexDirection: 'row', gap: 8, marginBottom: 14 },
  modalButton: {
    flex: 1, paddingVertical: 13, borderRadius: 10,
    alignItems: 'center', flexDirection: 'row',
    justifyContent: 'center', gap: 5,
  },
  cancelButton:      { backgroundColor: '#ecf0f1' },
  confirmButton:     { backgroundColor: '#2563eb' },
  cancelButtonText:  { color: '#7f8c8d', fontWeight: 'bold', fontSize: 15 },
  confirmButtonText: { color: 'white', fontWeight: 'bold', fontSize: 15 },
  modalFooter:       { paddingTop: 10, borderTopWidth: 1, borderTopColor: '#ecf0f1' },
  modalFooterText:   { color: '#95a5a6', fontSize: 12, textAlign: 'center' },

  saveModalScroll: { flexGrow: 1, justifyContent: 'center', alignItems: 'center', padding: 20 },
  saveModalContent: {
    backgroundColor: 'white', borderRadius: 16,
    padding: 20, width: '100%', maxWidth: 420,
  },
  skuTag: { fontSize: 12, color: '#3498db', fontWeight: '600', marginTop: 2 },

  infoBadgeNew: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 7,
    backgroundColor: '#eff6ff', borderWidth: 1, borderColor: '#93c5fd',
    borderRadius: 8, padding: 10, marginBottom: 14,
  },
  infoBadgeText: { fontSize: 12, color: '#1e40af', flex: 1, lineHeight: 17 },

  fieldLabel: { fontSize: 13, fontWeight: '600', color: '#2c3e50', marginBottom: 6, marginTop: 10 },
  fieldRow: {
    flexDirection: 'row', alignItems: 'center',
    borderWidth: 1.5, borderColor: '#e2e8f0',
    borderRadius: 8, backgroundColor: '#f9fafb', marginBottom: 2,
  },
  fieldIcon:  { paddingHorizontal: 10 },
  fieldInput: { flex: 1, paddingVertical: 12, paddingRight: 10, fontSize: 15, color: '#2c3e50' },
  fieldHint:  { fontSize: 11, color: '#94a3b8', marginTop: 6, marginBottom: 4, fontStyle: 'italic' },

  saveButtonRow: { flexDirection: 'row', gap: 8, marginTop: 18 },
  saveModalButton: {
    flex: 1, paddingVertical: 13, borderRadius: 10,
    alignItems: 'center', flexDirection: 'row',
    justifyContent: 'center', gap: 6,
  },
  saveButton:     { backgroundColor: '#2563eb' },
  disabledBtn:    { opacity: 0.6 },
  saveButtonText: { color: 'white', fontWeight: 'bold', fontSize: 15 },
});

export default ScanScreen;