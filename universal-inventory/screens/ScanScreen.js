// screens/ScanScreen.js
// Requiere: expo-camera >= SDK 50
import React, { useState, useRef, useCallback } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  Alert, TextInput, Modal, Vibration,
  KeyboardAvoidingView, Platform,
} from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { MaterialIcons } from '@expo/vector-icons';

// ── Modal definido FUERA del componente padre ─────────────────────────────────
// Si se define adentro, React lo trata como un tipo nuevo en cada render,
// lo desmonta/remonta con cada keystroke y el teclado desaparece.
const ManualEntryModal = ({ visible, manualCode, onChangeText, onSubmit, onClose, inputRef }) => (
  <Modal
    animationType="slide"
    transparent
    visible={visible}
    onRequestClose={onClose}
  >
    <KeyboardAvoidingView
      style={styles.modalContainer}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <View style={styles.modalContent}>
        <View style={styles.modalHeader}>
          <Text style={styles.modalTitle}>Entrada Manual</Text>
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
          returnKeyType="search"
          onSubmitEditing={onSubmit}
          blurOnSubmit={false}
        />

        <View style={styles.modalButtons}>
          <TouchableOpacity style={[styles.modalButton, styles.cancelButton]} onPress={onClose}>
            <Text style={styles.cancelButtonText}>Cancelar</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.modalButton, styles.confirmButton]} onPress={onSubmit}>
            <Text style={styles.confirmButtonText}>Buscar</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.modalFooter}>
          <Text style={styles.modalFooterText}>
            Formatos: EAN-13, EAN-8, Code 128, Code 39, UPC, QR
          </Text>
        </View>
      </View>
    </KeyboardAvoidingView>
  </Modal>
);

const ScanScreen = ({ navigation }) => {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned]           = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [manualCode, setManualCode]     = useState('');
  const [flashEnabled, setFlashEnabled] = useState(false);
  const [facing, setFacing]             = useState('back');

  const inputRef = useRef(null);

  const abrirModal = useCallback(() => {
    setManualCode('');
    setModalVisible(true);
    setTimeout(() => inputRef.current?.focus(), 300);
  }, []);

  const cerrarModal = useCallback(() => {
    setModalVisible(false);
    setManualCode('');
  }, []);

  const handleBarCodeScanned = ({ type, data }) => {
    if (scanned) return;
    setScanned(true);
    Vibration.vibrate(200);

    Alert.alert(
      '✅ Código Escaneado',
      `Código: ${data}\nTipo: ${type}`,
      [
        {
          text: 'Reintentar',
          onPress: () => setScanned(false),
          style: 'cancel',
        },
        {
          text: 'Ver Producto',
          onPress: () => {
            setScanned(false);
            navigation.navigate('ProductDetail', { sku: data });
          },
        },
      ]
    );
  };

  const handleManualEntry = () => {
    if (!manualCode.trim()) {
      Alert.alert('Error', 'Ingresa un código válido');
      return;
    }
    const code = manualCode.trim().toUpperCase();
    cerrarModal();
    // Delay para que el modal/teclado cierre limpiamente antes de navegar
    setTimeout(() => {
      navigation.navigate('ProductDetail', { sku: code });
    }, 300);
  };

  const handleSimulate = () => {
    Alert.alert(
      'Modo Simulación',
      'Selecciona un producto para simular',
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Laptop HP (LPT-HP-001)',
          onPress: () => handleBarCodeScanned({ type: 'ean13', data: 'LPT-HP-001' }),
        },
        {
          text: 'Monitor Dell (MON-DL-002)',
          onPress: () => handleBarCodeScanned({ type: 'ean13', data: 'MON-DL-002' }),
        },
      ]
    );
  };

  // ── Estados de permiso ─────────────────────────────────────────────────────
  if (!permission) {
    return (
      <View style={styles.centeredContainer}>
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
          onPress={abrirModal}
        >
          <Text style={styles.permissionButtonText}>Entrada Manual</Text>
        </TouchableOpacity>

        <ManualEntryModal
          visible={modalVisible}
          manualCode={manualCode}
          onChangeText={setManualCode}
          onSubmit={handleManualEntry}
          onClose={cerrarModal}
          inputRef={inputRef}
        />
      </View>
    );
  }

  // ── Vista principal con cámara ─────────────────────────────────────────────
  return (
    <View style={styles.container}>
      <CameraView
        style={StyleSheet.absoluteFillObject}
        facing={facing}
        enableTorch={flashEnabled}
        barcodeScannerSettings={{
          barcodeTypes: ['qr', 'ean13', 'ean8', 'code128', 'code39', 'upc_a', 'upc_e'],
        }}
        onBarcodeScanned={scanned ? undefined : handleBarCodeScanned}
      >
        <View style={styles.overlay}>

          {/* Header */}
          <View style={styles.header}>
            <TouchableOpacity style={styles.headerButton} onPress={() => navigation.goBack()}>
              <MaterialIcons name="arrow-back" size={24} color="white" />
            </TouchableOpacity>
            <View style={styles.headerControls}>
              <TouchableOpacity
                style={styles.headerButton}
                onPress={() => setFlashEnabled(v => !v)}
              >
                <MaterialIcons
                  name={flashEnabled ? 'flash-on' : 'flash-off'}
                  size={24} color="white"
                />
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.headerButton}
                onPress={() => setFacing(f => (f === 'back' ? 'front' : 'back'))}
              >
                <MaterialIcons name="flip-camera-android" size={24} color="white" />
              </TouchableOpacity>
            </View>
          </View>

          {/* Marco de escaneo */}
          <View style={styles.scanArea}>
            <View style={styles.scanFrame}>
              <View style={[styles.corner, styles.cornerTL]} />
              <View style={[styles.corner, styles.cornerTR]} />
              <View style={[styles.corner, styles.cornerBL]} />
              <View style={[styles.corner, styles.cornerBR]} />
            </View>
          </View>

          {/* Instrucciones */}
          <View style={styles.instructionContainer}>
            <Text style={styles.instructionText}>Apunta la cámara al código de barras</Text>
            <Text style={styles.instructionSubtext}>El escaneo será automático</Text>
          </View>

          {/* Botones */}
          <View style={styles.buttonContainer}>
            <TouchableOpacity style={styles.actionButton} onPress={handleSimulate}>
              <MaterialIcons name="science" size={20} color="white" />
              <Text style={styles.actionButtonText}>Simular</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.actionButton, styles.manualButton]}
              onPress={abrirModal}
            >
              <MaterialIcons name="keyboard" size={20} color="white" />
              <Text style={styles.actionButtonText}>Manual</Text>
            </TouchableOpacity>
          </View>

          {scanned && (
            <View style={styles.scannedIndicator}>
              <MaterialIcons name="check-circle" size={30} color="#2ecc71" />
              <Text style={styles.scannedText}>Código detectado</Text>
            </View>
          )}

        </View>
      </CameraView>

      <ManualEntryModal
        visible={modalVisible}
        manualCode={manualCode}
        onChangeText={setManualCode}
        onSubmit={handleManualEntry}
        onClose={cerrarModal}
        inputRef={inputRef}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#000' },
  centeredContainer: {
    flex: 1, justifyContent: 'center', alignItems: 'center',
    backgroundColor: '#f5f5f5', padding: 30,
  },
  infoText: { fontSize: 16, color: '#7f8c8d' },
  noPermissionText: { fontSize: 18, fontWeight: 'bold', color: '#2c3e50', marginTop: 15 },
  noPermissionSubtext: {
    fontSize: 14, color: '#7f8c8d', textAlign: 'center', marginTop: 8, marginBottom: 20,
  },
  permissionButton: {
    backgroundColor: '#3498db', paddingVertical: 14,
    paddingHorizontal: 30, borderRadius: 10,
  },
  permissionButtonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
  overlay: {
    flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'space-between',
  },
  header: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', padding: 20, paddingTop: 50,
  },
  headerButton: {
    width: 40, height: 40, borderRadius: 20,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center', alignItems: 'center',
  },
  headerControls: { flexDirection: 'row', gap: 8 },
  scanArea:  { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scanFrame: { width: 260, height: 260, position: 'relative' },
  corner:    { position: 'absolute', width: 30, height: 30, borderColor: '#3498db' },
  cornerTL:  { top: 0, left: 0, borderTopWidth: 3, borderLeftWidth: 3 },
  cornerTR:  { top: 0, right: 0, borderTopWidth: 3, borderRightWidth: 3 },
  cornerBL:  { bottom: 0, left: 0, borderBottomWidth: 3, borderLeftWidth: 3 },
  cornerBR:  { bottom: 0, right: 0, borderBottomWidth: 3, borderRightWidth: 3 },
  instructionContainer: { alignItems: 'center', marginBottom: 20 },
  instructionText:    { color: 'white', fontSize: 18, fontWeight: 'bold' },
  instructionSubtext: { color: 'rgba(255,255,255,0.8)', fontSize: 14, marginTop: 5 },
  buttonContainer: {
    flexDirection: 'row', justifyContent: 'space-around',
    marginBottom: 40, paddingHorizontal: 20,
  },
  actionButton: {
    backgroundColor: 'rgba(52,152,219,0.9)', paddingVertical: 12,
    paddingHorizontal: 20, borderRadius: 25,
    flexDirection: 'row', alignItems: 'center', width: '45%', justifyContent: 'center',
  },
  manualButton:     { backgroundColor: 'rgba(46,204,113,0.9)' },
  actionButtonText: { color: 'white', fontSize: 15, fontWeight: '600', marginLeft: 5 },
  scannedIndicator: {
    position: 'absolute', top: '50%', alignSelf: 'center',
    backgroundColor: 'white', padding: 15, borderRadius: 10,
    flexDirection: 'row', alignItems: 'center', elevation: 5,
  },
  scannedText: { marginLeft: 10, fontSize: 16, fontWeight: 'bold', color: '#2c3e50' },
  // Modal
  modalContainer: {
    flex: 1, justifyContent: 'center', alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalContent: {
    backgroundColor: 'white', borderRadius: 15,
    padding: 20, width: '85%', maxWidth: 400,
  },
  modalHeader: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', marginBottom: 20,
  },
  modalTitle:  { fontSize: 20, fontWeight: 'bold', color: '#2c3e50' },
  modalLabel:  { fontSize: 14, color: '#7f8c8d', marginBottom: 5 },
  modalInput: {
    borderWidth: 1, borderColor: '#bdc3c7',
    borderRadius: 10, padding: 15, fontSize: 16, marginBottom: 20,
  },
  modalButtons: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
  modalButton:  { flex: 1, padding: 15, borderRadius: 10, alignItems: 'center', marginHorizontal: 5 },
  cancelButton:      { backgroundColor: '#e74c3c' },
  confirmButton:     { backgroundColor: '#2ecc71' },
  cancelButtonText:  { color: 'white', fontWeight: 'bold', fontSize: 16 },
  confirmButtonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
  modalFooter:     { paddingTop: 10, borderTopWidth: 1, borderTopColor: '#ecf0f1' },
  modalFooterText: { color: '#95a5a6', fontSize: 12, textAlign: 'center' },
});

export default ScanScreen;