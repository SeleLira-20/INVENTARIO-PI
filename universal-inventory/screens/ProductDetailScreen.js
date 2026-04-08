// screens/ProductDetailScreen.js
import React, { useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, TouchableOpacity,
  SafeAreaView, Alert, ActivityIndicator, StatusBar, Platform,
  Modal, TextInput, KeyboardAvoidingView,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const API_BASE = 'http://192.168.18.218:8000';

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────
const getStockStatus = (item) => {
  if (item.stock_actual === 0)                         return { color: '#e74c3c', bg: '#fef2f2', text: 'Sin Stock',  icon: 'remove-circle' };
  if (item.stock_actual <= item.stock_minimo * 0.5)    return { color: '#e74c3c', bg: '#fef2f2', text: 'Crítico',   icon: 'error' };
  if (item.stock_actual <= item.stock_minimo)          return { color: '#f39c12', bg: '#fffbeb', text: 'Bajo',      icon: 'warning' };
  return                                                      { color: '#2ecc71', bg: '#f0fdf4', text: 'Normal',    icon: 'check-circle' };
};

const formatDate = (dateStr) => {
  if (!dateStr) return '—';
  try {
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-MX', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit',
    });
  } catch { return dateStr; }
};

// ─────────────────────────────────────────────────────────────────────────────
// Sub-componentes
// ─────────────────────────────────────────────────────────────────────────────

// Fila de detalle genérica
const DetailRow = ({ icon, iconColor, label, value, valueColor, valueWeight }) => (
  <View style={styles.detailRow}>
    <View style={styles.detailRowLeft}>
      <MaterialIcons name={icon} size={18} color={iconColor || '#94a3b8'} />
      <Text style={styles.detailLabel}>{label}</Text>
    </View>
    <Text style={[styles.detailValue, valueColor && { color: valueColor }, valueWeight && { fontWeight: valueWeight }]}>
      {value ?? '—'}
    </Text>
  </View>
);

// Tarjeta de sección
const SectionCard = ({ title, icon, children }) => (
  <View style={styles.sectionCard}>
    <View style={styles.sectionHeader}>
      <MaterialIcons name={icon} size={16} color="#64748b" />
      <Text style={styles.sectionTitle}>{title}</Text>
    </View>
    {children}
  </View>
);

// ─────────────────────────────────────────────────────────────────────────────
// Pantalla principal
// ─────────────────────────────────────────────────────────────────────────────
const ProductDetailScreen = ({ route, navigation }) => {
  const { product } = route.params;
  const [isSaving, setIsSaving]     = useState(false);
  const [stockModal, setStockModal] = useState(false);
  const [cantidad, setCantidad]     = useState('');
  const insets = useSafeAreaInsets();

  // Ocultar el header del navegador (evita el doble header)
  React.useLayoutEffect(() => {
    navigation.setOptions({ headerShown: false });
  }, [navigation]);

  const status = getStockStatus(product);

  // Barra de stock visual
  const maxRef    = Math.max(product.stock_minimo * 2, product.stock_actual, 1);
  const stockPct  = Math.min((product.stock_actual / maxRef) * 100, 100);
  const minPct    = Math.min((product.stock_minimo / maxRef) * 100, 100);

  // Valor total en inventario
  const valorTotal = (product.stock_actual * parseFloat(product.precio_unitario || 0)).toFixed(2);

  // ── Agregar stock con cantidad personalizada ──────────────────────────────
  const abrirModalStock = () => {
    setCantidad('');
    setStockModal(true);
  };

  const handleConfirmarStock = async () => {
    const n = parseInt(cantidad, 10);
    if (!n || n < 1 || n > 1000) {
      Alert.alert('Cantidad inválida', 'Ingresa un número entre 1 y 1000.');
      return;
    }
    setIsSaving(true);
    try {
      const nuevoStock = product.stock_actual + n;
      const res = await fetch(`${API_BASE}/v1/productos/${product.id_producto}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ stock_actual: nuevoStock }),
      });
      if (res.ok) {
        setStockModal(false);
        Alert.alert('✅ Actualizado', `Stock actualizado a ${nuevoStock} unidades.`, [
          { text: 'OK', onPress: () => navigation.goBack() },
        ]);
      } else {
        const err = await res.json().catch(() => ({}));
        Alert.alert('Error', err?.detail || `Error ${res.status}`);
      }
    } catch {
      Alert.alert('Error de conexión', 'No se pudo conectar con el servidor.');
    } finally {
      setIsSaving(false);
    }
  };

  // ── Eliminar ──────────────────────────────────────────────────────────────
  const handleDelete = () => {
    Alert.alert(
      'Eliminar producto',
      `¿Estás seguro de que deseas eliminar "${product.nombre}"? Esta acción no se puede deshacer.`,
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Eliminar', style: 'destructive',
          onPress: async () => {
            setIsSaving(true);
            try {
              const res = await fetch(`${API_BASE}/v1/productos/${product.id_producto}`, {
                method: 'DELETE',
              });
              if (res.ok || res.status === 204) {
                Alert.alert('Eliminado', `"${product.nombre}" fue eliminado.`, [
                  { text: 'OK', onPress: () => navigation.goBack() },
                ]);
              } else {
                Alert.alert('Error', `No se pudo eliminar. Código: ${res.status}`);
              }
            } catch {
              Alert.alert('Error de conexión', 'No se pudo conectar con el servidor.');
            } finally {
              setIsSaving(false);
            }
          },
        },
      ]
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#2563eb" />

      {/* ── Header ── */}
      <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <MaterialIcons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle} numberOfLines={1}>Detalle de producto</Text>
        <TouchableOpacity style={styles.deleteBtn} onPress={handleDelete} disabled={isSaving}>
          <MaterialIcons name="delete-outline" size={22} color="white" />
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>

        {/* ── Hero card ── */}
        <View style={[styles.heroCard, { borderLeftColor: status.color }]}>
          <View style={styles.heroTop}>
            <View style={{ flex: 1 }}>
              <Text style={styles.heroName}>{product.nombre}</Text>
              <View style={styles.skuRow}>
                <MaterialIcons name="qr-code" size={13} color="#94a3b8" />
                <Text style={styles.heroSku}> {product.sku}</Text>
              </View>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: status.bg }]}>
              <MaterialIcons name={status.icon} size={14} color={status.color} />
              <Text style={[styles.statusText, { color: status.color }]}>{status.text}</Text>
            </View>
          </View>

          {/* Barra de stock */}
          <View style={styles.stockBarWrapper}>
            <View style={styles.stockBarTrack}>
              {/* Marca del mínimo */}
              <View style={[styles.stockMinMark, { left: `${minPct}%` }]} />
              {/* Relleno actual */}
              <View style={[styles.stockBarFill, { width: `${stockPct}%`, backgroundColor: status.color }]} />
            </View>
            <View style={styles.stockBarLabels}>
              <Text style={styles.stockBarLabelLeft}>0</Text>
              <Text style={[styles.stockBarLabelMin, { left: `${minPct}%` }]}>
                mín.{product.stock_minimo}
              </Text>
              <Text style={styles.stockBarLabelRight}>{maxRef}</Text>
            </View>
          </View>

          {/* Métricas rápidas */}
          <View style={styles.metricsRow}>
            <View style={styles.metricBox}>
              <Text style={[styles.metricValue, { color: status.color }]}>{product.stock_actual}</Text>
              <Text style={styles.metricLabel}>Stock actual</Text>
            </View>
            <View style={styles.metricDivider} />
            <View style={styles.metricBox}>
              <Text style={styles.metricValue}>{product.stock_minimo}</Text>
              <Text style={styles.metricLabel}>Stock mínimo</Text>
            </View>
            <View style={styles.metricDivider} />
            <View style={styles.metricBox}>
              <Text style={[styles.metricValue, { color: '#2563eb' }]}>
                ${parseFloat(product.precio_unitario || 0).toFixed(2)}
              </Text>
              <Text style={styles.metricLabel}>Precio unit.</Text>
            </View>
          </View>
        </View>

        {/* ── Sección: Identificación ── */}
        <SectionCard title="Identificación" icon="fingerprint">
          <DetailRow icon="tag"          label="ID producto"  value={`#${product.id_producto}`} />
          <DetailRow icon="qr-code"      label="SKU"          value={product.sku} valueWeight="700" />
          <DetailRow icon="label"        label="Nombre"       value={product.nombre} />
          <DetailRow icon="category"     label="Categoría"    value={product.categoria || '—'} />
        </SectionCard>

        {/* ── Sección: Stock ── */}
        <SectionCard title="Stock y precio" icon="inventory">
          <DetailRow
            icon="layers"
            label="Stock actual"
            value={`${product.stock_actual} unidades`}
            valueColor={status.color}
            valueWeight="700"
          />
          <DetailRow icon="warning"      label="Stock mínimo"  value={`${product.stock_minimo} unidades`} />
          <DetailRow icon="attach-money" label="Precio unitario" value={`$${parseFloat(product.precio_unitario || 0).toFixed(2)}`} />
          <DetailRow
            icon="account-balance-wallet"
            label="Valor total en inventario"
            value={`$${valorTotal}`}
            valueColor="#2563eb"
            valueWeight="700"
          />
        </SectionCard>

        {/* ── Sección: Estado ── */}
        <SectionCard title="Estado del producto" icon="info">
          <DetailRow
            icon={status.icon}
            iconColor={status.color}
            label="Estado de stock"
            value={status.text}
            valueColor={status.color}
            valueWeight="700"
          />
          <DetailRow
            icon="circle"
            iconColor={product.estado === 'activo' ? '#2ecc71' : '#94a3b8'}
            label="Estado"
            value={product.estado || '—'}
            valueColor={product.estado === 'activo' ? '#16a34a' : '#64748b'}
          />
          {product.descripcion != null && (
            <DetailRow icon="notes" label="Descripción" value={product.descripcion || '—'} />
          )}
        </SectionCard>

        {/* ── Sección: Fechas (si existen) ── */}
        {(product.fecha_creacion || product.fecha_actualizacion || product.updated_at || product.created_at) && (
          <SectionCard title="Registro" icon="schedule">
            {(product.fecha_creacion || product.created_at) && (
              <DetailRow
                icon="add-circle-outline"
                label="Creado"
                value={formatDate(product.fecha_creacion || product.created_at)}
              />
            )}
            {(product.fecha_actualizacion || product.updated_at) && (
              <DetailRow
                icon="update"
                label="Última actualización"
                value={formatDate(product.fecha_actualizacion || product.updated_at)}
              />
            )}
          </SectionCard>
        )}

        {/* ── Sección: Campos extra (cualquier campo no mapeado) ── */}
        {(() => {
          const knownKeys = new Set([
            'id_producto','sku','nombre','categoria','stock_actual','stock_minimo',
            'precio_unitario','estado','descripcion',
            'fecha_creacion','fecha_actualizacion','created_at','updated_at',
          ]);
          const extras = Object.entries(product).filter(([k]) => !knownKeys.has(k));
          if (!extras.length) return null;
          return (
            <SectionCard title="Información adicional" icon="more-horiz">
              {extras.map(([key, val]) => (
                <DetailRow
                  key={key}
                  icon="chevron-right"
                  label={key.replace(/_/g, ' ')}
                  value={val != null ? String(val) : '—'}
                />
              ))}
            </SectionCard>
          );
        })()}

        {/* ── Aviso de stock bajo ── */}
        {(status.text === 'Crítico' || status.text === 'Sin Stock') && (
          <View style={[styles.alertBanner, { backgroundColor: '#fef2f2', borderColor: '#fca5a5' }]}>
            <MaterialIcons name="error" size={18} color="#e74c3c" />
            <Text style={styles.alertText}>
              {status.text === 'Sin Stock'
                ? 'Este producto está agotado. Considera reponer el inventario.'
                : `Stock crítico: quedan solo ${product.stock_actual} unidades (mínimo: ${product.stock_minimo}).`}
            </Text>
          </View>
        )}
        {status.text === 'Bajo' && (
          <View style={[styles.alertBanner, { backgroundColor: '#fffbeb', borderColor: '#fcd34d' }]}>
            <MaterialIcons name="warning" size={18} color="#f39c12" />
            <Text style={[styles.alertText, { color: '#92400e' }]}>
              Stock bajo: {product.stock_actual} unidades disponibles.
            </Text>
          </View>
        )}

        {/* Espaciado final */}
        <View style={{ height: 20 }} />
      </ScrollView>

      {/* ── Barra de acciones ── */}
      <View style={styles.actionBar}>
        <TouchableOpacity
          style={[styles.actionBtn, styles.actionBtnSecondary]}
          onPress={() => navigation.navigate('Scan')}
        >
          <MaterialIcons name="qr-code-scanner" size={20} color="#2563eb" />
          <Text style={[styles.actionBtnText, { color: '#2563eb' }]}>Escanear</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.actionBtn, styles.actionBtnPrimary, isSaving && { opacity: 0.6 }]}
          onPress={abrirModalStock}
          disabled={isSaving}
        >
          {isSaving
            ? <ActivityIndicator color="white" size="small" />
            : <>
                <MaterialIcons name="add-circle" size={20} color="white" />
                <Text style={[styles.actionBtnText, { color: 'white' }]}>Agregar stock</Text>
              </>
          }
        </TouchableOpacity>
      </View>

      {/* ── Modal agregar stock ── */}
      <Modal
        animationType="slide"
        transparent
        visible={stockModal}
        onRequestClose={() => !isSaving && setStockModal(false)}
      >
        <KeyboardAvoidingView
          style={mStyles.wrapper}
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        >
          <TouchableOpacity style={mStyles.backdrop} activeOpacity={1} onPress={() => !isSaving && setStockModal(false)} />
          <View style={mStyles.sheet}>
            <View style={mStyles.pill} />
            <View style={mStyles.header}>
              <MaterialIcons name="add-circle" size={26} color="#16a34a" />
              <View style={{ marginLeft: 10, flex: 1 }}>
                <Text style={mStyles.title}>Agregar stock</Text>
                <Text style={mStyles.subtitle} numberOfLines={1}>{product.nombre}</Text>
              </View>
              <TouchableOpacity onPress={() => setStockModal(false)} disabled={isSaving}>
                <MaterialIcons name="close" size={22} color="#64748b" />
              </TouchableOpacity>
            </View>

            <View style={mStyles.summaryRow}>
              <Text style={mStyles.summaryLabel}>Stock actual</Text>
              <Text style={mStyles.summaryValue}>{product.stock_actual} unid.</Text>
            </View>
            <View style={mStyles.summaryRow}>
              <Text style={mStyles.summaryLabel}>Nuevo stock</Text>
              <Text style={[mStyles.summaryValue, { color: '#16a34a', fontWeight: '700' }]}>
                {product.stock_actual + (parseInt(cantidad, 10) || 0)} unid.
              </Text>
            </View>

            <Text style={mStyles.label}>Unidades a agregar *</Text>
            <View style={[
              mStyles.inputRow,
              cantidad !== '' && (parseInt(cantidad, 10) < 1 || parseInt(cantidad, 10) > 1000) && { borderColor: '#e74c3c' },
            ]}>
              <MaterialIcons name="add-circle-outline" size={20} color="#16a34a" style={{ paddingHorizontal: 10 }} />
              <TextInput
                style={mStyles.input}
                placeholder="Ej: 10"
                value={cantidad}
                onChangeText={(v) => setCantidad(v.replace(/[^0-9]/g, ''))}
                keyboardType="number-pad"
                maxLength={4}
                autoFocus
              />
            </View>
            {cantidad !== '' && parseInt(cantidad, 10) > 1000 && (
              <Text style={mStyles.errorHint}>El máximo permitido es 1000 unidades</Text>
            )}
            {cantidad !== '' && (isNaN(parseInt(cantidad, 10)) || parseInt(cantidad, 10) < 1) && (
              <Text style={mStyles.errorHint}>Ingresa un número entre 1 y 1000</Text>
            )}
            <Text style={mStyles.hint}>Solo positivos · máximo 1 000 por operación</Text>

            <View style={mStyles.buttons}>
              <TouchableOpacity style={mStyles.cancelBtn} onPress={() => setStockModal(false)} disabled={isSaving}>
                <Text style={mStyles.cancelBtnText}>Cancelar</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[
                  mStyles.confirmBtn,
                  (isSaving || !cantidad || parseInt(cantidad, 10) < 1 || parseInt(cantidad, 10) > 1000) && mStyles.disabledBtn,
                ]}
                onPress={handleConfirmarStock}
                disabled={isSaving || !cantidad || parseInt(cantidad, 10) < 1 || parseInt(cantidad, 10) > 1000}
              >
                {isSaving
                  ? <ActivityIndicator color="white" size="small" />
                  : <>
                      <MaterialIcons name="add-circle" size={18} color="white" />
                      <Text style={mStyles.confirmBtnText}>Confirmar +{parseInt(cantidad, 10) || 0}</Text>
                    </>
                }
              </TouchableOpacity>
            </View>
          </View>
        </KeyboardAvoidingView>
      </Modal>

    </SafeAreaView>
  );
};

// ─────────────────────────────────────────────────────────────────────────────
// Estilos
// ─────────────────────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  container:   { flex: 1, backgroundColor: '#f1f5f9' },

  // Header
  header: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    backgroundColor: '#2563eb', paddingHorizontal: 16, paddingVertical: 14,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 10,
    backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center',
  },
  deleteBtn: {
    width: 36, height: 36, borderRadius: 10,
    backgroundColor: 'rgba(255,255,255,0.18)', justifyContent: 'center', alignItems: 'center',
  },
  headerTitle: { flex: 1, fontSize: 17, fontWeight: '700', color: 'white', textAlign: 'center', marginHorizontal: 8 },

  scroll: { padding: 16, paddingBottom: 0 },

  // Hero card
  heroCard: {
    backgroundColor: 'white', borderRadius: 16,
    padding: 18, marginBottom: 14,
    borderLeftWidth: 5,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07, shadowRadius: 8, elevation: 3,
  },
  heroTop: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 14 },
  heroName: { fontSize: 20, fontWeight: '800', color: '#1e293b', lineHeight: 26 },
  skuRow: { flexDirection: 'row', alignItems: 'center', marginTop: 4 },
  heroSku: { fontSize: 12, color: '#94a3b8', fontWeight: '600' },
  statusBadge: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    paddingHorizontal: 10, paddingVertical: 5,
    borderRadius: 20, marginLeft: 10,
  },
  statusText: { fontSize: 12, fontWeight: '700' },

  // Barra de stock
  stockBarWrapper: { marginBottom: 16 },
  stockBarTrack: {
    height: 8, backgroundColor: '#e2e8f0', borderRadius: 4,
    overflow: 'visible', position: 'relative',
  },
  stockBarFill: { height: '100%', borderRadius: 4 },
  stockMinMark: {
    position: 'absolute', top: -3, width: 2, height: 14,
    backgroundColor: '#f39c12', borderRadius: 1,
  },
  stockBarLabels: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 4, position: 'relative' },
  stockBarLabelLeft:  { fontSize: 10, color: '#94a3b8' },
  stockBarLabelRight: { fontSize: 10, color: '#94a3b8' },
  stockBarLabelMin: { position: 'absolute', fontSize: 10, color: '#f39c12', fontWeight: '600', transform: [{ translateX: -16 }] },

  // Métricas
  metricsRow: { flexDirection: 'row', alignItems: 'center' },
  metricBox: { flex: 1, alignItems: 'center' },
  metricValue: { fontSize: 22, fontWeight: '800', color: '#1e293b' },
  metricLabel: { fontSize: 11, color: '#94a3b8', marginTop: 2 },
  metricDivider: { width: 1, height: 36, backgroundColor: '#e2e8f0' },

  // Section card
  sectionCard: {
    backgroundColor: 'white', borderRadius: 14,
    padding: 16, marginBottom: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  sectionHeader: {
    flexDirection: 'row', alignItems: 'center', gap: 7,
    marginBottom: 12, paddingBottom: 10,
    borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  sectionTitle: { fontSize: 12, fontWeight: '700', color: '#64748b', textTransform: 'uppercase', letterSpacing: 0.6 },

  // Detail row
  detailRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', paddingVertical: 8,
    borderBottomWidth: 1, borderBottomColor: '#f8fafc',
  },
  detailRowLeft: { flexDirection: 'row', alignItems: 'center', gap: 8, flex: 1 },
  detailLabel:   { fontSize: 13, color: '#64748b' },
  detailValue:   { fontSize: 13, color: '#1e293b', textAlign: 'right', flex: 1, marginLeft: 8 },

  // Alert banner
  alertBanner: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 10,
    borderWidth: 1, borderRadius: 10, padding: 12, marginBottom: 12,
  },
  alertText: { flex: 1, fontSize: 13, color: '#991b1b', lineHeight: 18 },

  // Action bar
  actionBar: {
    flexDirection: 'row', gap: 12,
    paddingHorizontal: 16, paddingVertical: 12,
    backgroundColor: 'white',
    borderTopWidth: 1, borderTopColor: '#e2e8f0',
  },
  actionBtn: {
    flex: 1, flexDirection: 'row', alignItems: 'center',
    justifyContent: 'center', gap: 7,
    paddingVertical: 13, borderRadius: 12,
  },
  actionBtnPrimary:   { backgroundColor: '#16a34a' },
  actionBtnSecondary: { backgroundColor: '#eff6ff', borderWidth: 1.5, borderColor: '#bfdbfe' },
  actionBtnText: { fontSize: 15, fontWeight: '700' },
});

export default ProductDetailScreen;

const mStyles = StyleSheet.create({
  wrapper:  { flex: 1, justifyContent: 'flex-end' },
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)' },
  sheet: {
    backgroundColor: 'white',
    borderTopLeftRadius: 24, borderTopRightRadius: 24,
    paddingHorizontal: 20, paddingBottom: 36,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.15, shadowRadius: 12, elevation: 20,
  },
  pill: {
    width: 40, height: 4, borderRadius: 2,
    backgroundColor: '#cbd5e1', alignSelf: 'center',
    marginTop: 12, marginBottom: 16,
  },
  header: {
    flexDirection: 'row', alignItems: 'center',
    marginBottom: 16, paddingBottom: 16,
    borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  title:    { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  subtitle: { fontSize: 12, color: '#3498db', fontWeight: '600', marginTop: 2 },
  summaryRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  summaryLabel: { fontSize: 14, color: '#64748b' },
  summaryValue: { fontSize: 14, color: '#1e293b' },
  label:    { fontSize: 13, fontWeight: '600', color: '#2c3e50', marginTop: 16, marginBottom: 6 },
  inputRow: {
    flexDirection: 'row', alignItems: 'center',
    borderWidth: 1.5, borderColor: '#e2e8f0',
    borderRadius: 8, backgroundColor: '#f9fafb',
  },
  input:     { flex: 1, paddingVertical: 13, paddingRight: 10, fontSize: 16, color: '#2c3e50' },
  hint:      { fontSize: 11, color: '#94a3b8', marginTop: 6, fontStyle: 'italic' },
  errorHint: { fontSize: 11, color: '#e74c3c', marginTop: 4 },
  buttons:   { flexDirection: 'row', gap: 10, marginTop: 20 },
  cancelBtn: {
    flex: 1, paddingVertical: 13, borderRadius: 10,
    backgroundColor: '#ecf0f1', alignItems: 'center', justifyContent: 'center',
  },
  cancelBtnText: { color: '#7f8c8d', fontWeight: 'bold', fontSize: 15 },
  confirmBtn: {
    flex: 1, paddingVertical: 13, borderRadius: 10,
    backgroundColor: '#16a34a',
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6,
  },
  confirmBtnText: { color: 'white', fontWeight: 'bold', fontSize: 15 },
  disabledBtn:    { opacity: 0.45 },
});