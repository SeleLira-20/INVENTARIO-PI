import React, { useState, useEffect } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  SafeAreaView, ActivityIndicator, Alert, TextInput, KeyboardAvoidingView, Platform
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';

const API_BASE = 'http://192.168.100.38:8000';

const getStockStatus = (item) => {
  if (item.cantidad <= item.stockMinimo * 0.5) return { color: '#e74c3c', text: 'Crítico', bg: '#fdf0ef' };
  if (item.cantidad <= item.stockMinimo)        return { color: '#f39c12', text: 'Bajo',    bg: '#fef9ef' };
  return { color: '#2ecc71', text: 'Normal', bg: '#edfbf3' };
};

const InfoRow = ({ icon, label, value, color }) => (
  <View style={styles.infoRow}>
    <View style={styles.infoLeft}>
      <MaterialIcons name={icon} size={20} color="#7f8c8d" />
      <Text style={styles.infoLabel}>{label}</Text>
    </View>
    <Text style={[styles.infoValue, color && { color }]}>{value}</Text>
  </View>
);

const ProductDetailScreen = ({ route, navigation }) => {
  // Accept either a full product object (from InventoryScreen) or just a sku (from ScanScreen)
  const { product: initialProduct, sku } = route.params ?? {};

  const [product, setProduct] = useState(initialProduct ?? null);
  const [loading, setLoading] = useState(!initialProduct);
  const [saving, setSaving] = useState(false);
  const [editMode, setEditMode] = useState(false);

  // Editable fields
  const [cantidad, setCantidad] = useState('');
  const [ubicacion, setUbicacion] = useState('');
  const [stockMinimo, setStockMinimo] = useState('');
  const [stockMaximo, setStockMaximo] = useState('');

  // Load product by SKU when coming from ScanScreen
  useEffect(() => {
    if (!initialProduct && sku) {
      fetchBySku(sku);
    }
  }, []);

  // Populate edit fields when product loads
  useEffect(() => {
    if (product) {
      setCantidad(String(product.cantidad));
      setUbicacion(product.ubicacion);
      setStockMinimo(String(product.stockMinimo));
      setStockMaximo(String(product.stockMaximo));
    }
  }, [product]);

  const fetchBySku = async (skuCode) => {
    try {
      setLoading(true);
      // Fetch all and find by SKU (API doesn't have SKU endpoint)
      const res = await fetch(`${API_BASE}/materiales`);
      const data = await res.json();
      const found = data.find(m => m.sku === skuCode);
      if (found) {
        setProduct(found);
      } else {
        Alert.alert('No encontrado', `No existe un producto con SKU: ${skuCode}`, [
          { text: 'Volver', onPress: () => navigation.goBack() },
        ]);
      }
    } catch (e) {
      Alert.alert('Error', 'No se pudo conectar con el servidor');
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    const cantNum = parseInt(cantidad, 10);
    const minNum  = parseInt(stockMinimo, 10);
    const maxNum  = parseInt(stockMaximo, 10);

    if (isNaN(cantNum) || isNaN(minNum) || isNaN(maxNum)) {
      Alert.alert('Error', 'Los valores de cantidad y stock deben ser números');
      return;
    }
    if (cantNum < 0 || minNum < 0 || maxNum < 0) {
      Alert.alert('Error', 'Los valores no pueden ser negativos');
      return;
    }
    if (maxNum < minNum) {
      Alert.alert('Error', 'El stock máximo no puede ser menor al mínimo');
      return;
    }

    // Determine new estado
    let estado = 'stock_normal';
    if (cantNum <= minNum * 0.5) estado = 'stock_critico';
    else if (cantNum <= minNum)  estado = 'stock_bajo';

    const today = new Date().toISOString().split('T')[0];

    const updated = {
      ...product,
      cantidad: cantNum,
      ubicacion: ubicacion.trim(),
      stockMinimo: minNum,
      stockMaximo: maxNum,
      estado,
      ultimaActualizacion: today,
    };

    try {
      setSaving(true);
      const res = await fetch(`${API_BASE}/materiales/${product.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updated),
      });

      if (!res.ok) throw new Error('Server error');

      setProduct(updated);
      setEditMode(false);
      Alert.alert('✅ Guardado', 'El producto fue actualizado correctamente');
    } catch (e) {
      Alert.alert('Error', 'No se pudo guardar. Verifica la conexión.');
    } finally {
      setSaving(false);
    }
  };

  const handleCancelEdit = () => {
    // Restore original values
    setCantidad(String(product.cantidad));
    setUbicacion(product.ubicacion);
    setStockMinimo(String(product.stockMinimo));
    setStockMaximo(String(product.stockMaximo));
    setEditMode(false);
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <MaterialIcons name="arrow-back" size={24} color="#2c3e50" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Detalle del Producto</Text>
          <View style={{ width: 24 }} />
        </View>
        <View style={styles.centered}>
          <ActivityIndicator size="large" color="#3498db" />
          <Text style={styles.loadingText}>Buscando producto...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!product) return null;

  const status = getStockStatus(product);
  const fillPct = Math.min((product.cantidad / product.stockMaximo) * 100, 100);

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <MaterialIcons name="arrow-back" size={24} color="#2c3e50" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Detalle del Producto</Text>
        {!editMode ? (
          <TouchableOpacity onPress={() => setEditMode(true)}>
            <MaterialIcons name="edit" size={24} color="#3498db" />
          </TouchableOpacity>
        ) : (
          <TouchableOpacity onPress={handleCancelEdit}>
            <MaterialIcons name="close" size={24} color="#e74c3c" />
          </TouchableOpacity>
        )}
      </View>

      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">

          {/* Product title card */}
          <View style={[styles.titleCard, { borderLeftColor: status.color }]}>
            <View style={styles.titleRow}>
              <View style={{ flex: 1 }}>
                <Text style={styles.productName}>{product.nombre}</Text>
                <Text style={styles.productSku}>SKU: {product.sku}</Text>
              </View>
              <View style={[styles.statusBadge, { backgroundColor: status.bg }]}>
                <Text style={[styles.statusText, { color: status.color }]}>{status.text}</Text>
              </View>
            </View>
          </View>

          {/* Stock card */}
          <View style={styles.card}>
            <Text style={styles.cardTitle}>
              <MaterialIcons name="inventory" size={16} color="#2c3e50" /> Stock
            </Text>

            <View style={styles.stockNumbers}>
              <View style={styles.stockBox}>
                <Text style={styles.stockLabel}>Actual</Text>
                {editMode ? (
                  <TextInput
                    style={[styles.stockInput, { color: status.color }]}
                    value={cantidad}
                    onChangeText={setCantidad}
                    keyboardType="numeric"
                    selectTextOnFocus
                  />
                ) : (
                  <Text style={[styles.stockValue, { color: status.color }]}>{product.cantidad}</Text>
                )}
                <Text style={styles.stockUnit}>unid.</Text>
              </View>
              <View style={styles.stockDivider} />
              <View style={styles.stockBox}>
                <Text style={styles.stockLabel}>Mínimo</Text>
                {editMode ? (
                  <TextInput
                    style={styles.stockInput}
                    value={stockMinimo}
                    onChangeText={setStockMinimo}
                    keyboardType="numeric"
                    selectTextOnFocus
                  />
                ) : (
                  <Text style={styles.stockValue}>{product.stockMinimo}</Text>
                )}
                <Text style={styles.stockUnit}>unid.</Text>
              </View>
              <View style={styles.stockDivider} />
              <View style={styles.stockBox}>
                <Text style={styles.stockLabel}>Máximo</Text>
                {editMode ? (
                  <TextInput
                    style={styles.stockInput}
                    value={stockMaximo}
                    onChangeText={setStockMaximo}
                    keyboardType="numeric"
                    selectTextOnFocus
                  />
                ) : (
                  <Text style={styles.stockValue}>{product.stockMaximo}</Text>
                )}
                <Text style={styles.stockUnit}>unid.</Text>
              </View>
            </View>

            {/* Progress bar */}
            <View style={styles.progressTrack}>
              <View style={[styles.progressFill, { width: `${fillPct}%`, backgroundColor: status.color }]} />
            </View>
            <Text style={styles.progressLabel}>{Math.round(fillPct)}% del máximo</Text>
          </View>

          {/* Info card */}
          <View style={styles.card}>
            <Text style={styles.cardTitle}>
              <MaterialIcons name="info" size={16} color="#2c3e50" /> Información
            </Text>

            <InfoRow icon="category"    label="Categoría"  value={product.categoria} />
            <View style={styles.separator} />

            <View style={styles.infoRow}>
              <View style={styles.infoLeft}>
                <MaterialIcons name="location-on" size={20} color="#7f8c8d" />
                <Text style={styles.infoLabel}>Ubicación</Text>
              </View>
              {editMode ? (
                <TextInput
                  style={styles.inlineInput}
                  value={ubicacion}
                  onChangeText={setUbicacion}
                  autoCapitalize="characters"
                  selectTextOnFocus
                />
              ) : (
                <Text style={styles.infoValue}>{product.ubicacion}</Text>
              )}
            </View>
            <View style={styles.separator} />

            <InfoRow icon="local-shipping" label="Proveedor"  value={product.proveedor} />
            <View style={styles.separator} />
            <InfoRow icon="update"          label="Actualizado" value={product.ultimaActualizacion} />
            <View style={styles.separator} />
            <InfoRow icon="tag"             label="ID"         value={`#${product.id}`} color="#95a5a6" />
          </View>

          {/* Save button */}
          {editMode && (
            <TouchableOpacity
              style={[styles.saveButton, saving && styles.saveButtonDisabled]}
              onPress={handleSave}
              disabled={saving}
            >
              {saving ? (
                <ActivityIndicator color="white" />
              ) : (
                <>
                  <MaterialIcons name="save" size={20} color="white" />
                  <Text style={styles.saveButtonText}>Guardar Cambios</Text>
                </>
              )}
            </TouchableOpacity>
          )}

          <View style={{ height: 30 }} />
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container:    { flex: 1, backgroundColor: '#f5f5f5' },
  centered:     { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingText:  { marginTop: 10, color: '#7f8c8d' },
  header: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', padding: 20,
    backgroundColor: 'white', borderBottomWidth: 1, borderBottomColor: '#ecf0f1',
  },
  headerTitle: { fontSize: 18, fontWeight: 'bold', color: '#2c3e50' },
  scroll:       { padding: 15 },

  // Title card
  titleCard: {
    backgroundColor: 'white', borderRadius: 12, padding: 16,
    marginBottom: 12, borderLeftWidth: 4, elevation: 2,
  },
  titleRow:     { flexDirection: 'row', alignItems: 'flex-start' },
  productName:  { fontSize: 18, fontWeight: 'bold', color: '#2c3e50', flex: 1 },
  productSku:   { fontSize: 13, color: '#7f8c8d', marginTop: 4 },
  statusBadge:  { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12, marginLeft: 10 },
  statusText:   { fontSize: 13, fontWeight: 'bold' },

  // Generic card
  card: {
    backgroundColor: 'white', borderRadius: 12,
    padding: 16, marginBottom: 12, elevation: 2,
  },
  cardTitle: { fontSize: 15, fontWeight: 'bold', color: '#2c3e50', marginBottom: 14 },

  // Stock row
  stockNumbers: { flexDirection: 'row', justifyContent: 'space-around', marginBottom: 14 },
  stockBox:     { alignItems: 'center', flex: 1 },
  stockDivider: { width: 1, backgroundColor: '#ecf0f1' },
  stockLabel:   { fontSize: 12, color: '#7f8c8d', marginBottom: 4 },
  stockValue:   { fontSize: 26, fontWeight: 'bold', color: '#2c3e50' },
  stockUnit:    { fontSize: 11, color: '#95a5a6', marginTop: 2 },
  stockInput: {
    fontSize: 26, fontWeight: 'bold', color: '#3498db',
    borderBottomWidth: 2, borderBottomColor: '#3498db',
    textAlign: 'center', minWidth: 60, paddingVertical: 2,
  },

  // Progress
  progressTrack: { height: 8, backgroundColor: '#ecf0f1', borderRadius: 4, marginBottom: 6 },
  progressFill:  { height: '100%', borderRadius: 4 },
  progressLabel: { fontSize: 12, color: '#95a5a6', textAlign: 'right' },

  // Info rows
  infoRow:   { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 8 },
  infoLeft:  { flexDirection: 'row', alignItems: 'center' },
  infoLabel: { fontSize: 14, color: '#7f8c8d', marginLeft: 8 },
  infoValue: { fontSize: 14, color: '#2c3e50', fontWeight: '500', maxWidth: '55%', textAlign: 'right' },
  separator: { height: 1, backgroundColor: '#f0f0f0' },

  // Inline edit input (for ubicacion)
  inlineInput: {
    fontSize: 14, color: '#3498db', fontWeight: '500',
    borderBottomWidth: 1.5, borderBottomColor: '#3498db',
    textAlign: 'right', minWidth: 80, paddingVertical: 2,
  },

  // Save button
  saveButton: {
    backgroundColor: '#3498db', borderRadius: 12, padding: 16,
    flexDirection: 'row', justifyContent: 'center', alignItems: 'center',
    marginTop: 4, elevation: 3,
  },
  saveButtonDisabled: { opacity: 0.6 },
  saveButtonText: { color: 'white', fontWeight: 'bold', fontSize: 16, marginLeft: 8 },
});

export default ProductDetailScreen;