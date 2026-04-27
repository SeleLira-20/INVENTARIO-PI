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

// ── Cambia esta IP por la de tu servidor ──────────────────────────────────────
const API_BASE = 'https://inventario-pi-1.onrender.com';
const BARCODE_TYPES = ['ean13', 'ean8', 'code128', 'code39', 'upc_a', 'upc_e', 'itf14', 'codabar'];

const CATEGORIAS = [
  { label: 'Electrónica',       icon: 'devices' },
  { label: 'Cómputo',           icon: 'computer' },
  { label: 'Periféricos',       icon: 'keyboard' },
  { label: 'Telefonía',         icon: 'smartphone' },
  { label: 'Audio y Video',     icon: 'headset' },
  { label: 'Oficina',           icon: 'business-center' },
  { label: 'Muebles',           icon: 'chair' },
  { label: 'Herramientas',      icon: 'build' },
  { label: 'Ropa y Calzado',    icon: 'checkroom' },
  { label: 'Alimentos',         icon: 'local-dining' },
  { label: 'Limpieza',          icon: 'cleaning-services' },
  { label: 'Salud y Belleza',   icon: 'medical-services' },
  { label: 'Juguetes',          icon: 'toys' },
  { label: 'Deportes',          icon: 'fitness-center' },
  { label: 'Automotriz',        icon: 'directions-car' },
  { label: 'General',           icon: 'category' },
];

// ─────────────────────────────────────────────────────────────────────────────
// Selector de categoría con desplegable
// ─────────────────────────────────────────────────────────────────────────────
const CategoryPicker = ({ value, onChange }) => {
  const [open, setOpen] = useState(false);
  const selected = CATEGORIAS.find(c => c.label === value);

  return (
    <>
      <TouchableOpacity
        style={[styles.fieldRow, { paddingRight: 10 }]}
        onPress={() => setOpen(true)}
        activeOpacity={0.7}
      >
        <MaterialIcons
          name={selected ? selected.icon : 'category'}
          size={20} color={value ? '#2563eb' : '#7f8c8d'}
          style={styles.fieldIcon}
        />
        <Text style={[styles.fieldInput, { paddingVertical: 13, color: value ? '#2c3e50' : '#aaa' }]}>
          {value || 'Selecciona una categoría'}
        </Text>
        <MaterialIcons name="expand-more" size={22} color="#94a3b8" />
      </TouchableOpacity>

      <Modal animationType="fade" transparent visible={open} onRequestClose={() => setOpen(false)}>
        <TouchableOpacity
          style={styles.pickerOverlay}
          activeOpacity={1}
          onPress={() => setOpen(false)}
        >
          <View style={styles.pickerSheet}>
            <View style={styles.pickerHeader}>
              <Text style={styles.pickerTitle}>Selecciona una categoría</Text>
              <TouchableOpacity onPress={() => setOpen(false)}>
                <MaterialIcons name="close" size={22} color="#64748b" />
              </TouchableOpacity>
            </View>
            <ScrollView showsVerticalScrollIndicator={false}>
              {CATEGORIAS.map(cat => {
                const isActive = cat.label === value;
                return (
                  <TouchableOpacity
                    key={cat.label}
                    style={[styles.pickerItem, isActive && styles.pickerItemActive]}
                    onPress={() => { onChange(cat.label); setOpen(false); }}
                  >
                    <View style={[styles.pickerItemIcon, isActive && { backgroundColor: '#eff6ff' }]}>
                      <MaterialIcons name={cat.icon} size={20} color={isActive ? '#2563eb' : '#64748b'} />
                    </View>
                    <Text style={[styles.pickerItemText, isActive && { color: '#2563eb', fontWeight: '700' }]}>
                      {cat.label}
                    </Text>
                    {isActive && <MaterialIcons name="check" size={18} color="#2563eb" />}
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          </View>
        </TouchableOpacity>
      </Modal>
    </>
  );
};

// ─────────────────────────────────────────────────────────────────────────────
// Helper: estado de stock (igual que InventoryScreen)
// ─────────────────────────────────────────────────────────────────────────────
const getStockStatus = (item) => {
  if (item.stock_actual === 0)                       return { color: '#e74c3c', text: 'Sin Stock' };
  if (item.stock_actual <= item.stock_minimo * 0.5)  return { color: '#e74c3c', text: 'Crítico' };
  if (item.stock_actual <= item.stock_minimo)        return { color: '#f39c12', text: 'Bajo' };
  return { color: '#2ecc71', text: 'Normal' };
};

// ─────────────────────────────────────────────────────────────────────────────
// Modal 1 — Entrada manual de código
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
            <MaterialIcons name="search" size={18} color="white" />
            <Text style={styles.confirmButtonText}>Continuar</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.modalFooter}>
          <Text style={styles.modalFooterText}>Formatos: EAN-13, EAN-8, Code 128, Code 39, UPC</Text>
        </View>
      </View>
    </KeyboardAvoidingView>
  </Modal>
);

// ─────────────────────────────────────────────────────────────────────────────
// Modal 2 — Elegir acción: Buscar o Agregar
// Aparece siempre tras escanear/ingresar un código
// ─────────────────────────────────────────────────────────────────────────────
const ActionChoiceModal = ({ visible, sku, onSearch, onAdd, onClose }) => (
  <Modal animationType="fade" transparent visible={visible} onRequestClose={onClose}>
    <View style={styles.modalContainer}>
      <View style={styles.choiceContent}>

        <View style={styles.modalHeader}>
          <View style={{ flex: 1 }}>
            <Text style={styles.modalTitle}>Código detectado</Text>
            <Text style={styles.skuTag}>📦 {sku}</Text>
          </View>
          <TouchableOpacity onPress={onClose}>
            <MaterialIcons name="close" size={24} color="#7f8c8d" />
          </TouchableOpacity>
        </View>

        <Text style={styles.choiceQuestion}>¿Qué deseas hacer?</Text>

        {/* Buscar */}
        <TouchableOpacity style={[styles.choiceButton, styles.choiceSearch]} onPress={onSearch}>
          <View style={[styles.choiceIcon, { backgroundColor: '#eff6ff' }]}>
            <MaterialIcons name="search" size={26} color="#2563eb" />
          </View>
          <View style={styles.choiceText}>
            <Text style={styles.choiceTitle}>Buscar producto</Text>
            <Text style={styles.choiceSubtitle}>Ver stock, precio y detalles</Text>
          </View>
          <MaterialIcons name="chevron-right" size={22} color="#94a3b8" />
        </TouchableOpacity>

        {/* Agregar */}
        <TouchableOpacity style={[styles.choiceButton, styles.choiceAdd]} onPress={onAdd}>
          <View style={[styles.choiceIcon, { backgroundColor: '#f0fdf4' }]}>
            <MaterialIcons name="add-circle" size={26} color="#16a34a" />
          </View>
          <View style={styles.choiceText}>
            <Text style={styles.choiceTitle}>Agregar al inventario</Text>
            <Text style={styles.choiceSubtitle}>Registrar entrada o crear producto</Text>
          </View>
          <MaterialIcons name="chevron-right" size={22} color="#94a3b8" />
        </TouchableOpacity>

      </View>
    </View>
  </Modal>
);

// ─────────────────────────────────────────────────────────────────────────────
// Modal 3A — Resultado de búsqueda
// ─────────────────────────────────────────────────────────────────────────────
const SearchResultModal = ({ visible, onClose, result, sku, onGoToInventory }) => {
  if (!visible) return null;
  const status = result ? getStockStatus(result) : null;

  return (
    <Modal animationType="slide" transparent visible={visible} onRequestClose={onClose}>
      <KeyboardAvoidingView
        style={styles.addModalWrapper}
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      >
        {/* Fondo semitransparente arriba */}
        <TouchableOpacity style={styles.addModalBackdrop} onPress={onClose} />

        <View style={styles.addModalSheet}>

          {/* Pill indicador de bottom sheet */}
          <View style={styles.sheetPill} />

          {/* Header */}
          <View style={styles.addModalHeader}>
            <View style={styles.addModalHeaderLeft}>
              <MaterialIcons
                name={result ? 'search' : 'search-off'}
                size={28}
                color={result ? '#2563eb' : '#ea580c'}
              />
              <View style={{ marginLeft: 10 }}>
                <Text style={styles.addModalTitle}>Resultado</Text>
                <Text style={styles.skuTag}>SKU: {sku}</Text>
              </View>
            </View>
            <TouchableOpacity style={styles.addModalCloseBtn} onPress={onClose}>
              <MaterialIcons name="close" size={22} color="#64748b" />
            </TouchableOpacity>
          </View>

          <ScrollView
            showsVerticalScrollIndicator={false}
            keyboardShouldPersistTaps="handled"
            contentContainerStyle={styles.addModalScroll}
          >
            {result ? (
              <View>
                <View style={[styles.infoBadge, { backgroundColor: '#f0fdf4', borderColor: '#86efac' }]}>
                  <MaterialIcons name="check-circle" size={15} color="#16a34a" />
                  <Text style={[styles.infoBadgeText, { color: '#166534' }]}>
                    Producto encontrado en inventario
                  </Text>
                </View>

                <View style={styles.productSummaryCard}>
                  <ResultRow icon="label"        label="Nombre"       value={result.nombre} />
                  <ResultRow icon="qr-code"      label="SKU"          value={result.sku} />
                  <ResultRow
                    icon="inventory" label="Stock actual"
                    value={`${result.stock_actual} unid.`}
                    valueColor={status.color}
                  />
                  <ResultRow icon="warning"      label="Stock mínimo" value={`${result.stock_minimo} unid.`} />
                  <ResultRow icon="attach-money" label="Precio"       value={`$${parseFloat(result.precio_unitario).toFixed(2)}`} />
                  <ResultRow
                    icon="circle" label="Estado stock"
                    value={status.text} valueColor={status.color} iconColor={status.color}
                  />
                  {result.estado && <ResultRow icon="info" label="Estado" value={result.estado} />}
                </View>

                <View style={styles.addModalButtons}>
                  <TouchableOpacity
                    style={[styles.addModalBtn, styles.cancelFullButton]}
                    onPress={onClose}
                  >
                    <Text style={styles.cancelButtonText}>Cerrar</Text>
                  </TouchableOpacity>
                  <TouchableOpacity
                    style={[styles.addModalBtn, { backgroundColor: '#2563eb' }]}
                    onPress={onGoToInventory}
                  >
                    <MaterialIcons name="list" size={20} color="white" />
                    <Text style={styles.fullButtonText}>Ver en Inventario</Text>
                  </TouchableOpacity>
                </View>
              </View>
            ) : (
              <View>
                <View style={[styles.infoBadge, { backgroundColor: '#fff7ed', borderColor: '#fed7aa' }]}>
                  <MaterialIcons name="search-off" size={15} color="#ea580c" />
                  <Text style={[styles.infoBadgeText, { color: '#9a3412' }]}>
                    No se encontró ningún producto con este código.
                  </Text>
                </View>
                <Text style={styles.fieldHint}>
                  Puedes escanear de nuevo y elegir "Agregar al inventario" para crear este producto.
                </Text>

                <View style={styles.addModalButtons}>
                  <TouchableOpacity
                    style={[styles.addModalBtn, styles.cancelFullButton]}
                    onPress={onClose}
                  >
                    <Text style={styles.cancelButtonText}>Cerrar</Text>
                  </TouchableOpacity>
                </View>
              </View>
            )}
          </ScrollView>

        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
};

// ─────────────────────────────────────────────────────────────────────────────
// Modal 3B — Agregar al inventario (pantalla completa tipo bottom sheet)
// Si el producto existe: muestra +N y confirma con PUT /v1/productos/{id}
// Si no existe: formulario completo y guarda con POST /v1/productos/
// ─────────────────────────────────────────────────────────────────────────────
const AddToInventoryModal = ({
  visible, onClose, sku, existingProduct,
  form, onChangeForm, onConfirmAdd, onSaveNew, isSaving,
  cantidadAgregar, onChangeCantidad,
}) => (
  <Modal animationType="slide" transparent visible={visible} onRequestClose={onClose}>
    <KeyboardAvoidingView
      style={styles.addModalWrapper}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      {/* Fondo semitransparente pequeño arriba */}
      <TouchableOpacity style={styles.addModalBackdrop} onPress={!isSaving ? onClose : undefined} />

      <View style={styles.addModalSheet}>

        {/* Pill indicador de bottom sheet */}
        <View style={styles.sheetPill} />

        {/* Header */}
        <View style={styles.addModalHeader}>
          <View style={styles.addModalHeaderLeft}>
            {existingProduct
              ? <MaterialIcons name="add-circle" size={28} color="#16a34a" />
              : <MaterialIcons name="inventory" size={28} color="#2563eb" />
            }
            <View style={{ marginLeft: 10 }}>
              <Text style={styles.addModalTitle}>
                {existingProduct ? 'Agregar unidad' : 'Producto nuevo'}
              </Text>
              <Text style={styles.skuTag}>SKU: {sku}</Text>
            </View>
          </View>
          <TouchableOpacity
            style={styles.addModalCloseBtn}
            onPress={onClose} disabled={isSaving}
          >
            <MaterialIcons name="close" size={22} color="#64748b" />
          </TouchableOpacity>
        </View>

        <ScrollView
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled"
          contentContainerStyle={styles.addModalScroll}
        >
          {existingProduct ? (
            // ── Producto EXISTENTE → confirmar +N ───────────────────────────
            <View>
              <View style={[styles.infoBadge, { backgroundColor: '#eff6ff', borderColor: '#93c5fd' }]}>
                <MaterialIcons name="check-circle" size={15} color="#2563eb" />
                <Text style={styles.infoBadgeText}>
                  Producto encontrado. Indica cuántas unidades deseas agregar al stock.
                </Text>
              </View>

              {/* Tarjeta resumen del producto */}
              <View style={styles.productSummaryCard}>
                <ResultRow icon="label"     label="Nombre"      value={existingProduct.nombre} />
                <ResultRow icon="inventory" label="Stock actual" value={`${existingProduct.stock_actual} unid.`} />
                <ResultRow
                  icon="add-circle" label="Nuevo stock"
                  value={`${existingProduct.stock_actual + (parseInt(cantidadAgregar, 10) || 0)} unid.`}
                  valueColor="#16a34a" iconColor="#16a34a"
                />
              </View>

              {/* Input de cantidad */}
              <Text style={styles.fieldLabel}>Unidades a agregar *</Text>
              <View style={[styles.fieldRow, cantidadAgregar !== '' && (parseInt(cantidadAgregar, 10) < 1 || parseInt(cantidadAgregar, 10) > 1000) && { borderColor: '#e74c3c' }]}>
                <MaterialIcons name="add-circle-outline" size={20} color="#16a34a" style={styles.fieldIcon} />
                <TextInput
                  style={styles.fieldInput}
                  placeholder="Ej: 10"
                  value={cantidadAgregar}
                  onChangeText={onChangeCantidad}
                  keyboardType="number-pad"
                  maxLength={4}
                />
              </View>
              {cantidadAgregar !== '' && (parseInt(cantidadAgregar, 10) < 1 || isNaN(parseInt(cantidadAgregar, 10))) && (
                <Text style={[styles.fieldHint, { color: '#e74c3c' }]}>Ingresa un número entre 1 y 1000</Text>
              )}
              {cantidadAgregar !== '' && parseInt(cantidadAgregar, 10) > 1000 && (
                <Text style={[styles.fieldHint, { color: '#e74c3c' }]}>El máximo permitido es 1000 unidades</Text>
              )}
              <Text style={styles.fieldHint}>Solo números positivos, máximo 1000 por operación</Text>

              <View style={styles.addModalButtons}>
                <TouchableOpacity
                  style={[styles.addModalBtn, styles.cancelFullButton]}
                  onPress={onClose} disabled={isSaving}
                >
                  <Text style={styles.cancelButtonText}>Cancelar</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[
                    styles.addModalBtn, { backgroundColor: '#16a34a' },
                    (isSaving || !cantidadAgregar || parseInt(cantidadAgregar, 10) < 1 || parseInt(cantidadAgregar, 10) > 1000) && styles.disabledBtn
                  ]}
                  onPress={onConfirmAdd}
                  disabled={isSaving || !cantidadAgregar || parseInt(cantidadAgregar, 10) < 1 || parseInt(cantidadAgregar, 10) > 1000}
                >
                  {isSaving
                    ? <ActivityIndicator color="white" size="small" />
                    : <><MaterialIcons name="add-circle" size={20} color="white" /><Text style={styles.fullButtonText}>Confirmar +{parseInt(cantidadAgregar, 10) || 0}</Text></>
                  }
                </TouchableOpacity>
              </View>
            </View>

          ) : (
            // ── Producto NUEVO → formulario completo ─────────────────────────
            <View>
              <View style={[styles.infoBadge, { backgroundColor: '#fff7ed', borderColor: '#fed7aa' }]}>
                <MaterialIcons name="info-outline" size={15} color="#ea580c" />
                <Text style={[styles.infoBadgeText, { color: '#9a3412' }]}>
                  Código no registrado. Completa los datos para crearlo en el inventario.
                </Text>
              </View>

              {/* Sección: Información básica */}
              <Text style={styles.formSectionTitle}>Información básica</Text>

              <Text style={styles.fieldLabel}>Nombre del producto *</Text>
              <View style={styles.fieldRow}>
                <MaterialIcons name="label" size={20} color="#7f8c8d" style={styles.fieldIcon} />
                <TextInput
                  style={styles.fieldInput}
                  placeholder="Ej: Laptop HP EliteBook"
                  value={form.nombre}
                  onChangeText={v => onChangeForm('nombre', v)}
                />
              </View>

              <Text style={styles.fieldLabel}>Categoría</Text>
              <CategoryPicker
                value={form.categoria}
                onChange={v => onChangeForm('categoria', v)}
              />

              {/* Sección: Precios y stock — en fila de 2 */}
              <Text style={styles.formSectionTitle}>Precio y stock</Text>

              <View style={styles.twoColumnRow}>
                <View style={{ flex: 1, marginRight: 8 }}>
                  <Text style={styles.fieldLabel}>Precio unitario *</Text>
                  <View style={styles.fieldRow}>
                    <MaterialIcons name="attach-money" size={20} color="#7f8c8d" style={styles.fieldIcon} />
                    <TextInput
                      style={styles.fieldInput}
                      placeholder="0.00"
                      value={form.precio_unitario}
                      onChangeText={v => onChangeForm('precio_unitario', v)}
                      keyboardType="decimal-pad"
                    />
                  </View>
                </View>
                <View style={{ flex: 1, marginLeft: 8 }}>
                  <Text style={styles.fieldLabel}>Estado</Text>
                  <View style={styles.fieldRow}>
                    <MaterialIcons name="circle" size={20} color="#7f8c8d" style={styles.fieldIcon} />
                    <TextInput
                      style={styles.fieldInput}
                      placeholder="activo"
                      value={form.estado}
                      onChangeText={v => onChangeForm('estado', v)}
                    />
                  </View>
                </View>
              </View>

              <View style={styles.twoColumnRow}>
                <View style={{ flex: 1, marginRight: 8 }}>
                  <Text style={styles.fieldLabel}>Stock inicial *</Text>
                  <View style={styles.fieldRow}>
                    <MaterialIcons name="inventory" size={20} color="#7f8c8d" style={styles.fieldIcon} />
                    <TextInput
                      style={styles.fieldInput}
                      placeholder="10"
                      value={form.stock_actual}
                      onChangeText={v => onChangeForm('stock_actual', v)}
                      keyboardType="numeric"
                    />
                  </View>
                </View>
                <View style={{ flex: 1, marginLeft: 8 }}>
                  <Text style={styles.fieldLabel}>Stock mínimo *</Text>
                  <View style={styles.fieldRow}>
                    <MaterialIcons name="warning" size={20} color="#7f8c8d" style={styles.fieldIcon} />
                    <TextInput
                      style={styles.fieldInput}
                      placeholder="5"
                      value={form.stock_minimo}
                      onChangeText={v => onChangeForm('stock_minimo', v)}
                      keyboardType="numeric"
                    />
                  </View>
                </View>
              </View>

              <Text style={styles.requiredNote}>* Campos obligatorios</Text>

              <View style={styles.addModalButtons}>
                <TouchableOpacity
                  style={[styles.addModalBtn, styles.cancelFullButton]}
                  onPress={onClose} disabled={isSaving}
                >
                  <Text style={styles.cancelButtonText}>Cancelar</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[styles.addModalBtn, { backgroundColor: '#2563eb' }, isSaving && styles.disabledBtn]}
                  onPress={onSaveNew} disabled={isSaving}
                >
                  {isSaving
                    ? <ActivityIndicator color="white" size="small" />
                    : <><MaterialIcons name="save" size={20} color="white" /><Text style={styles.fullButtonText}>Guardar producto</Text></>
                  }
                </TouchableOpacity>
              </View>
            </View>
          )}
        </ScrollView>
      </View>
    </KeyboardAvoidingView>
  </Modal>
);

// ─────────────────────────────────────────────────────────────────────────────
// Componente auxiliar: fila de detalle reutilizable
// ─────────────────────────────────────────────────────────────────────────────
const ResultRow = ({ icon, label, value, valueColor, iconColor }) => (
  <View style={styles.resultRow}>
    <MaterialIcons name={icon} size={15} color={iconColor || '#7f8c8d'} />
    <Text style={styles.resultLabel}>{label}</Text>
    <Text style={[styles.resultValue, valueColor && { color: valueColor, fontWeight: '700' }]}>
      {value}
    </Text>
  </View>
);

// ─────────────────────────────────────────────────────────────────────────────
// Componente principal
// ─────────────────────────────────────────────────────────────────────────────
const ScanScreen = ({ navigation }) => {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned]           = useState(false);
  const [flashEnabled, setFlashEnabled] = useState(false);
  const [facing, setFacing]             = useState('back');
  const [isProcessing, setIsProcessing] = useState(false);

  // Modal 1: entrada manual
  const [manualModalVisible, setManualModalVisible] = useState(false);
  const [manualCode, setManualCode]                 = useState('');
  const inputRef = useRef(null);

  // Modal 2: elegir acción
  const [choiceModal, setChoiceModal] = useState(false);
  const [currentSku, setCurrentSku]   = useState('');

  // Modal 3A: resultado búsqueda
  const [searchModal, setSearchModal]   = useState(false);
  const [searchResult, setSearchResult] = useState(null);

  // Modal 3B: agregar al inventario
  const [addModal, setAddModal]               = useState(false);
  const [existingProduct, setExistingProduct] = useState(null);
  const [isSaving, setIsSaving]               = useState(false);
  const [cantidadAgregar, setCantidadAgregar] = useState('');

  // Formulario producto nuevo
  const [form, setForm] = useState({
    nombre: '', precio_unitario: '', stock_actual: '',
    stock_minimo: '', categoria: '', estado: 'activo',
  });

  // Toast
  const [toast, setToast] = useState(null);
  const toastTimerRef     = useRef(null);

  // ── Helpers ───────────────────────────────────────────────────────────────
  const updateForm = useCallback((key, value) => {
    setForm(prev => ({ ...prev, [key]: value }));
  }, []);

  const showToast = useCallback((msg, type = 'success') => {
    if (toastTimerRef.current) clearTimeout(toastTimerRef.current);
    setToast({ msg, type });
    toastTimerRef.current = setTimeout(() => setToast(null), 3000);
  }, []);

  const resetScan = useCallback(() => {
    setScanned(false);
    setCurrentSku('');
    setExistingProduct(null);
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

  // ── Buscar en la API por SKU ──────────────────────────────────────────────
  // Usa GET /v1/productos/ y filtra en cliente por sku
  const buscarProductoPorSku = useCallback(async (sku) => {
    const res  = await fetch(`${API_BASE}/v1/productos/`);
    const data = await res.json();
    const lista = data.productos || [];
    return lista.find(p => p.sku?.toUpperCase() === sku.toUpperCase()) || null;
  }, []);

  // ── Paso 1: código detectado → modal de elección ─────────────────────────
  const handleCodigo = useCallback((sku) => {
    setCurrentSku(sku);
    setChoiceModal(true);
  }, []);

  // ── Paso 2A: usuario elige BUSCAR ─────────────────────────────────────────
  const handleElegirBuscar = useCallback(async () => {
    setChoiceModal(false);
    setIsProcessing(true);
    try {
      const found = await buscarProductoPorSku(currentSku);
      setSearchResult(found);
      setSearchModal(true);
    } catch {
      showToast('❌ Sin conexión al servidor', 'error');
      resetScan();
    } finally {
      setIsProcessing(false);
    }
  }, [currentSku, buscarProductoPorSku, showToast, resetScan]);

  // ── Paso 2B: usuario elige AGREGAR ────────────────────────────────────────
  const handleElegirAgregar = useCallback(async () => {
    setChoiceModal(false);
    setIsProcessing(true);
    try {
      const found = await buscarProductoPorSku(currentSku);
      setExistingProduct(found);
      setForm({ nombre: '', precio_unitario: '', stock_actual: '', stock_minimo: '', categoria: '', estado: 'activo' });
      setCantidadAgregar('');
      setAddModal(true);
    } catch {
      showToast('❌ Sin conexión al servidor', 'error');
      resetScan();
    } finally {
      setIsProcessing(false);
    }
  }, [currentSku, buscarProductoPorSku, showToast, resetScan]);

  // ── Confirmar +N a producto EXISTENTE — PUT /v1/productos/{id} ──────────
  const handleConfirmAdd = useCallback(async () => {
    if (!existingProduct) return;
    const cantidad = parseInt(cantidadAgregar, 10);
    if (!cantidad || cantidad < 1 || cantidad > 1000) {
      Alert.alert('Cantidad inválida', 'Ingresa un número entre 1 y 1000.');
      return;
    }
    setIsSaving(true);
    try {
      const nuevoStock = existingProduct.stock_actual + cantidad;
      const res = await fetch(`${API_BASE}/v1/productos/${existingProduct.id_producto}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ stock_actual: nuevoStock }),
      });

      if (res.ok) {
        setAddModal(false);
        showToast(`✅ ${existingProduct.nombre} → ${nuevoStock} unid. (+${cantidad})`, 'success');
        resetScan();
      } else {
        const err = await res.json().catch(() => ({}));
        Alert.alert('Error del servidor', err?.detail || `Error ${res.status}`);
      }
    } catch {
      Alert.alert('Error de conexión', 'No se pudo conectar con el servidor.');
    } finally {
      setIsSaving(false);
    }
  }, [existingProduct, cantidadAgregar, showToast, resetScan]);

  // ── Guardar producto NUEVO — POST /v1/productos/ ───────────────────────────
  const handleSaveNew = useCallback(async () => {
    if (!form.nombre.trim())          { Alert.alert('Error', 'Ingresa el nombre del producto'); return; }
    if (!form.precio_unitario.trim()) { Alert.alert('Error', 'Ingresa el precio unitario'); return; }
    if (!form.stock_actual.trim())    { Alert.alert('Error', 'Ingresa el stock inicial'); return; }
    if (!form.stock_minimo.trim())    { Alert.alert('Error', 'Ingresa el stock mínimo'); return; }

    setIsSaving(true);
    try {
      const body = {
        sku:             currentSku,
        nombre:          form.nombre.trim(),
        precio_unitario: parseFloat(form.precio_unitario) || 0,
        stock_actual:    parseInt(form.stock_actual, 10)  || 0,
        stock_minimo:    parseInt(form.stock_minimo, 10)  || 0,
        categoria:       form.categoria.trim() || 'General',
        estado:          form.estado.trim()    || 'activo',
      };

      const res = await fetch(`${API_BASE}/v1/productos/`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });

      if (res.ok) {
        setAddModal(false);
        showToast(`✅ "${form.nombre.trim()}" agregado al inventario`, 'success');
        resetScan();
      } else {
        const err = await res.json().catch(() => ({}));
        Alert.alert('Error del servidor', err?.detail || `Error ${res.status}`);
      }
    } catch {
      Alert.alert('Error de conexión', 'No se pudo conectar con el servidor.');
    } finally {
      setIsSaving(false);
    }
  }, [form, currentSku, showToast, resetScan]);

  // ── Cámara ────────────────────────────────────────────────────────────────
  const handleBarCodeScanned = useCallback(({ data }) => {
    if (scanned || isProcessing) return;
    setScanned(true);
    Vibration.vibrate(120);
    handleCodigo(data.trim().toUpperCase());
  }, [scanned, isProcessing, handleCodigo]);

  // ── Entrada manual ────────────────────────────────────────────────────────
  const handleManualEntry = useCallback(() => {
    if (!manualCode.trim()) { Alert.alert('Error', 'Ingresa un código válido'); return; }
    const code = manualCode.trim().toUpperCase();
    cerrarManualModal();
    setTimeout(() => handleCodigo(code), 350);
  }, [manualCode, cerrarManualModal, handleCodigo]);

  // ── Simulación ────────────────────────────────────────────────────────────
  const handleSimulate = () => {
    Alert.alert('Modo Simulación', 'Selecciona un código', [
      { text: 'Cancelar', style: 'cancel' },
      { text: 'SKU existente (LPT-HP-001)', onPress: () => handleBarCodeScanned({ data: 'LPT-HP-001' }) },
      { text: 'SKU nuevo (TEST-999)',        onPress: () => handleBarCodeScanned({ data: 'TEST-999' }) },
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
      </View>
    );
  }

  // ── Vista principal ───────────────────────────────────────────────────────
  return (
    <View style={styles.container}>

      {isProcessing && (
        <View style={styles.processingOverlay}>
          <ActivityIndicator size="large" color="white" />
          <Text style={styles.processingText}>Consultando inventario...</Text>
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
              Podrás buscar el producto o agregarlo al inventario
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

      {/* Modal 1 — Entrada manual */}
      <ManualEntryModal
        visible={manualModalVisible} manualCode={manualCode}
        onChangeText={setManualCode} onSubmit={handleManualEntry}
        onClose={cerrarManualModal} inputRef={inputRef}
      />

      {/* Modal 2 — Elegir acción */}
      <ActionChoiceModal
        visible={choiceModal}
        sku={currentSku}
        onSearch={handleElegirBuscar}
        onAdd={handleElegirAgregar}
        onClose={() => { setChoiceModal(false); resetScan(); }}
      />

      {/* Modal 3A — Resultado de búsqueda */}
      <SearchResultModal
        visible={searchModal}
        sku={currentSku}
        result={searchResult}
        onClose={() => { setSearchModal(false); resetScan(); }}
        onGoToInventory={() => { setSearchModal(false); resetScan(); navigation.navigate('Inventory'); }}
      />

      {/* Modal 3B — Agregar al inventario */}
      <AddToInventoryModal
        visible={addModal}
        sku={currentSku}
        existingProduct={existingProduct}
        form={form}
        onChangeForm={updateForm}
        onConfirmAdd={handleConfirmAdd}
        onSaveNew={handleSaveNew}
        isSaving={isSaving}
        cantidadAgregar={cantidadAgregar}
        onChangeCantidad={(v) => {
          // Solo dígitos, sin negativos, máximo 4 caracteres
          const clean = v.replace(/[^0-9]/g, '');
          setCantidadAgregar(clean);
        }}
        onClose={() => { setAddModal(false); resetScan(); }}
      />

    </View>
  );
};

// ─────────────────────────────────────────────────────────────────────────────
// Estilos
// ─────────────────────────────────────────────────────────────────────────────
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
  noPermissionSubtext: { fontSize: 14, color: '#7f8c8d', textAlign: 'center', marginTop: 8, marginBottom: 20 },
  permissionButton: { backgroundColor: '#3498db', paddingVertical: 14, paddingHorizontal: 30, borderRadius: 10 },
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

  buttonContainer: { flexDirection: 'row', justifyContent: 'space-around', marginBottom: 40, paddingHorizontal: 16 },
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
    alignItems: 'center', flexDirection: 'row', justifyContent: 'center', gap: 5,
  },
  cancelButton:      { backgroundColor: '#ecf0f1' },
  confirmButton:     { backgroundColor: '#2563eb' },
  cancelButtonText:  { color: '#7f8c8d', fontWeight: 'bold', fontSize: 15 },
  confirmButtonText: { color: 'white', fontWeight: 'bold', fontSize: 15 },
  modalFooter:       { paddingTop: 10, borderTopWidth: 1, borderTopColor: '#ecf0f1' },
  modalFooterText:   { color: '#95a5a6', fontSize: 12, textAlign: 'center' },

  choiceContent: {
    backgroundColor: 'white', borderRadius: 20,
    padding: 20, width: '88%', maxWidth: 420,
  },
  choiceQuestion: { fontSize: 14, color: '#64748b', marginBottom: 14 },
  choiceButton: {
    flexDirection: 'row', alignItems: 'center', gap: 14,
    borderWidth: 1.5, borderRadius: 14,
    padding: 14, marginBottom: 10,
  },
  choiceSearch: { borderColor: '#bfdbfe', backgroundColor: '#f8faff' },
  choiceAdd:    { borderColor: '#bbf7d0', backgroundColor: '#f8fff9' },
  choiceIcon: {
    width: 48, height: 48, borderRadius: 12,
    justifyContent: 'center', alignItems: 'center',
  },
  choiceText: { flex: 1 },
  choiceTitle:    { fontSize: 15, fontWeight: '700', color: '#1e293b' },
  choiceSubtitle: { fontSize: 12, color: '#64748b', marginTop: 2 },

  saveModalScroll: { flexGrow: 1, justifyContent: 'center', alignItems: 'center', padding: 16 },
  saveModalContent: {
    backgroundColor: 'white', borderRadius: 16,
    padding: 20, width: '100%', maxWidth: 420,
  },
  skuTag: { fontSize: 12, color: '#3498db', fontWeight: '600', marginTop: 2 },

  // ── Bottom sheet pantalla completa (AddToInventoryModal) ──────────────────
  addModalWrapper:  { flex: 1, justifyContent: 'flex-end' },
  addModalBackdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)' },
  addModalSheet: {
    backgroundColor: 'white',
    borderTopLeftRadius: 24, borderTopRightRadius: 24,
    paddingHorizontal: 20, paddingBottom: 34,
    maxHeight: '92%',
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.15, shadowRadius: 12, elevation: 20,
  },
  sheetPill: {
    width: 40, height: 4, borderRadius: 2,
    backgroundColor: '#cbd5e1',
    alignSelf: 'center', marginTop: 12, marginBottom: 16,
  },
  addModalHeader: {
    flexDirection: 'row', alignItems: 'center',
    justifyContent: 'space-between', marginBottom: 16,
    paddingBottom: 16, borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  addModalHeaderLeft: { flexDirection: 'row', alignItems: 'center', flex: 1 },
  addModalTitle:      { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  addModalCloseBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: '#f1f5f9',
    justifyContent: 'center', alignItems: 'center',
  },
  addModalScroll:  { paddingBottom: 20 },
  addModalButtons: { flexDirection: 'row', gap: 10, marginTop: 24 },
  addModalBtn: {
    flex: 1, paddingVertical: 15, borderRadius: 12,
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 7,
  },
  productSummaryCard: {
    backgroundColor: '#f8fafc', borderRadius: 12,
    borderWidth: 1, borderColor: '#e2e8f0',
    paddingHorizontal: 16, paddingVertical: 4, marginBottom: 8,
  },
  twoColumnRow:    { flexDirection: 'row', marginTop: 2 },
  formSectionTitle: {
    fontSize: 12, fontWeight: '700', color: '#64748b',
    textTransform: 'uppercase', letterSpacing: 0.6,
    marginTop: 20, marginBottom: 4,
  },
  requiredNote: { fontSize: 11, color: '#94a3b8', marginTop: 12, fontStyle: 'italic' },

  infoBadge: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 7,
    borderWidth: 1, borderRadius: 8, padding: 10, marginBottom: 14,
  },
  infoBadgeText: { fontSize: 12, flex: 1, lineHeight: 17 },

  resultRow: {
    flexDirection: 'row', alignItems: 'center', gap: 8,
    paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  resultLabel: { fontSize: 13, color: '#64748b', width: 110 },
  resultValue: { fontSize: 13, color: '#1e293b', flex: 1 },

  fieldLabel: { fontSize: 13, fontWeight: '600', color: '#2c3e50', marginBottom: 6, marginTop: 10 },
  fieldRow: {
    flexDirection: 'row', alignItems: 'center',
    borderWidth: 1.5, borderColor: '#e2e8f0',
    borderRadius: 8, backgroundColor: '#f9fafb', marginBottom: 2,
  },
  fieldIcon:  { paddingHorizontal: 10 },
  fieldInput: { flex: 1, paddingVertical: 12, paddingRight: 10, fontSize: 15, color: '#2c3e50' },
  fieldHint:  { fontSize: 11, color: '#94a3b8', marginTop: 6, marginBottom: 4, fontStyle: 'italic' },

  fullButton: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    gap: 6, paddingVertical: 13, borderRadius: 10,
  },
  fullButtonText:   { color: 'white', fontWeight: 'bold', fontSize: 15 },
  cancelFullButton: { backgroundColor: '#ecf0f1' },

  saveButtonRow: { flexDirection: 'row', gap: 8, marginTop: 18 },
  halfButton: {
    flex: 1, paddingVertical: 13, borderRadius: 10,
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
  },
  disabledBtn: { opacity: 0.6 },

  // ── Category Picker ───────────────────────────────────────────────────────
  pickerOverlay: {
    flex: 1, backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  pickerSheet: {
    backgroundColor: 'white',
    borderTopLeftRadius: 20, borderTopRightRadius: 20,
    paddingHorizontal: 16, paddingBottom: 34,
    maxHeight: '70%',
  },
  pickerHeader: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', paddingVertical: 16,
    borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
    marginBottom: 6,
  },
  pickerTitle: { fontSize: 16, fontWeight: '700', color: '#1e293b' },
  pickerItem: {
    flexDirection: 'row', alignItems: 'center', gap: 12,
    paddingVertical: 11, paddingHorizontal: 6,
    borderRadius: 10, marginBottom: 2,
  },
  pickerItemActive: { backgroundColor: '#f0f7ff' },
  pickerItemIcon: {
    width: 36, height: 36, borderRadius: 8,
    backgroundColor: '#f1f5f9',
    justifyContent: 'center', alignItems: 'center',
  },
  pickerItemText: { flex: 1, fontSize: 15, color: '#334155' },
});

export default ScanScreen;