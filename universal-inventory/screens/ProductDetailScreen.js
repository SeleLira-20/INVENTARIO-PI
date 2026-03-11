// screens/ProductDetailScreen.js
import React from 'react';
import {
  View, Text, StyleSheet, ScrollView,
  TouchableOpacity, SafeAreaView,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';

const getStockStatus = (item) => {
  if (item.cantidad <= item.stockMinimo * 0.5) return { color: '#e74c3c', bg: '#fdf0ef', text: 'Crítico' };
  if (item.cantidad <= item.stockMinimo)        return { color: '#f39c12', bg: '#fef9ef', text: 'Bajo'    };
  return                                               { color: '#2ecc71', bg: '#edfbf3', text: 'Normal'  };
};

const InfoRow = ({ icon, label, value }) => (
  <View style={styles.infoRow}>
    <View style={styles.infoLeft}>
      <MaterialIcons name={icon} size={18} color="#7f8c8d" />
      <Text style={styles.infoLabel}>{label}</Text>
    </View>
    <Text style={styles.infoValue} numberOfLines={1}>{value}</Text>
  </View>
);

const Divider = () => <View style={styles.divider} />;

const ProductDetailScreen = ({ route, navigation }) => {
  const { product } = route.params;
  const status  = getStockStatus(product);
  const fillPct = Math.min((product.cantidad / product.stockMaximo) * 100, 100);

  return (
    <SafeAreaView style={styles.container}>

      {/* ── Header ─────────────────────────────────────────────────────── */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <MaterialIcons name="arrow-back" size={24} color="#2c3e50" />
        </TouchableOpacity>
        <Text style={styles.headerTitle} numberOfLines={1}>Detalle del Producto</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>

        {/* ── Nombre + badge de estado ─────────────────────────────────── */}
        <View style={[styles.titleCard, { borderLeftColor: status.color }]}>
          <View style={styles.titleRow}>
            <View style={{ flex: 1 }}>
              <Text style={styles.productName}>{product.nombre}</Text>
              <Text style={styles.productSku}>SKU: {product.sku}</Text>
            </View>
            <View style={[styles.badge, { backgroundColor: status.bg }]}>
              <Text style={[styles.badgeText, { color: status.color }]}>{status.text}</Text>
            </View>
          </View>
        </View>

        {/* ── Stock: actual / mínimo / máximo ──────────────────────────── */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>📦  Stock</Text>

          <View style={styles.stockRow}>
            <View style={styles.stockBox}>
              <Text style={styles.stockLabel}>Actual</Text>
              <Text style={[styles.stockValue, { color: status.color }]}>{product.cantidad}</Text>
              <Text style={styles.stockUnit}>unid.</Text>
            </View>
            <View style={styles.stockSep} />
            <View style={styles.stockBox}>
              <Text style={styles.stockLabel}>Mínimo</Text>
              <Text style={styles.stockValue}>{product.stockMinimo}</Text>
              <Text style={styles.stockUnit}>unid.</Text>
            </View>
            <View style={styles.stockSep} />
            <View style={styles.stockBox}>
              <Text style={styles.stockLabel}>Máximo</Text>
              <Text style={styles.stockValue}>{product.stockMaximo}</Text>
              <Text style={styles.stockUnit}>unid.</Text>
            </View>
          </View>

          {/* Barra de progreso */}
          <View style={styles.progressTrack}>
            <View style={[styles.progressFill, { width: `${fillPct}%`, backgroundColor: status.color }]} />
          </View>
          <Text style={styles.progressLabel}>{Math.round(fillPct)}% del máximo</Text>
        </View>

        {/* ── Información del producto ──────────────────────────────────── */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>ℹ️  Información</Text>

          <InfoRow icon="category"       label="Categoría"   value={product.categoria} />
          <Divider />
          <InfoRow icon="location-on"    label="Ubicación"   value={product.ubicacion} />
          <Divider />
          <InfoRow icon="local-shipping" label="Proveedor"   value={product.proveedor} />
          <Divider />
          <InfoRow icon="update"         label="Actualizado" value={product.ultimaActualizacion} />
          <Divider />
          <InfoRow icon="tag"            label="ID interno"  value={`#${product.id}`} />
        </View>

        {/* ── Banner de estado ─────────────────────────────────────────── */}
        <View style={[styles.estadoCard, { backgroundColor: status.bg, borderColor: status.color }]}>
          <MaterialIcons name="info-outline" size={18} color={status.color} />
          <Text style={[styles.estadoText, { color: status.color }]}>
            Estado:{' '}
            <Text style={{ fontWeight: '800' }}>{status.text}</Text>
            {product.estado === 'stock_critico' && ' — Reabastecimiento urgente recomendado'}
            {product.estado === 'stock_bajo'    && ' — Considerar reabastecer pronto'}
            {product.estado === 'stock_normal'  && ' — Niveles dentro del rango esperado'}
          </Text>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },

  header: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingHorizontal: 16, paddingVertical: 14,
    backgroundColor: 'white', borderBottomWidth: 1, borderBottomColor: '#ecf0f1',
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: '#f5f5f5', justifyContent: 'center', alignItems: 'center',
  },
  headerTitle: {
    fontSize: 17, fontWeight: '700', color: '#2c3e50', flex: 1, textAlign: 'center',
  },

  scroll: { padding: 15, paddingBottom: 40 },

  // Nombre
  titleCard: {
    backgroundColor: 'white', borderRadius: 12, padding: 16,
    marginBottom: 12, borderLeftWidth: 4, elevation: 2,
  },
  titleRow:    { flexDirection: 'row', alignItems: 'flex-start' },
  productName: { fontSize: 17, fontWeight: '800', color: '#2c3e50', marginBottom: 4, flexShrink: 1 },
  productSku:  { fontSize: 13, color: '#7f8c8d' },
  badge: {
    paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12,
    marginLeft: 10, alignSelf: 'flex-start',
  },
  badgeText: { fontSize: 12, fontWeight: '700' },

  // Card genérica
  card: {
    backgroundColor: 'white', borderRadius: 12, padding: 16,
    marginBottom: 12, elevation: 2,
  },
  cardTitle: { fontSize: 14, fontWeight: '700', color: '#2c3e50', marginBottom: 16 },

  // Stock
  stockRow:  { flexDirection: 'row', justifyContent: 'space-around', marginBottom: 16 },
  stockBox:  { alignItems: 'center', flex: 1 },
  stockSep:  { width: 1, backgroundColor: '#ecf0f1' },
  stockLabel: { fontSize: 12, color: '#7f8c8d', marginBottom: 4 },
  stockValue: { fontSize: 28, fontWeight: '800', color: '#2c3e50' },
  stockUnit:  { fontSize: 11, color: '#95a5a6', marginTop: 2 },

  // Barra de progreso
  progressTrack: { height: 8, backgroundColor: '#ecf0f1', borderRadius: 4, marginBottom: 6 },
  progressFill:  { height: '100%', borderRadius: 4 },
  progressLabel: { fontSize: 12, color: '#95a5a6', textAlign: 'right' },

  // Filas de información
  infoRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', paddingVertical: 10,
  },
  infoLeft:  { flexDirection: 'row', alignItems: 'center', flex: 1 },
  infoLabel: { fontSize: 14, color: '#7f8c8d', marginLeft: 8 },
  infoValue: { fontSize: 14, color: '#2c3e50', fontWeight: '600', maxWidth: '55%', textAlign: 'right' },
  divider:   { height: 1, backgroundColor: '#f0f0f0' },

  // Banner de estado
  estadoCard: {
    flexDirection: 'row', alignItems: 'flex-start',
    borderRadius: 12, padding: 14, borderWidth: 1, gap: 8,
  },
  estadoText: { flex: 1, fontSize: 13, lineHeight: 19 },
});

export default ProductDetailScreen;