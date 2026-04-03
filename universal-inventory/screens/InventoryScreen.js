import React, { useState, useEffect } from 'react';
import {
  View, Text, StyleSheet, FlatList, TextInput,
  TouchableOpacity, SafeAreaView, ActivityIndicator, RefreshControl
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';

// ── Usa los campos reales de tu API ──────────────────────
const getStockStatus = (item) => {
  if (item.stock_actual === 0)                          return { color: '#e74c3c', text: 'Sin Stock' };
  if (item.stock_actual <= item.stock_minimo * 0.5)     return { color: '#e74c3c', text: 'Crítico' };
  if (item.stock_actual <= item.stock_minimo)           return { color: '#f39c12', text: 'Bajo' };
  return { color: '#2ecc71', text: 'Normal' };
};

const InventoryScreen = ({ navigation }) => {
  const [searchText, setSearchText]   = useState('');
  const [loading, setLoading]         = useState(false);
  const [refreshing, setRefreshing]   = useState(false);
  const [filterType, setFilterType]   = useState('todos');
  const [inventory, setInventory]     = useState([]);

  const obtenerProductos = async () => {
    try {
      setLoading(true);
      const response = await fetch("http://192.168.100.99:8000/v1/productos/");
      const data = await response.json();
      // La API devuelve { status, total, productos: [...] }
      setInventory(data.productos || []);
    } catch (error) {
      console.log("Error cargando inventario:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    obtenerProductos();
  }, []);

  const onRefresh = async () => {
    setRefreshing(true);
    await obtenerProductos();
    setRefreshing(false);
  };

  // Filtros usando los campos reales de la API
  const filterMap = {
    todos:    () => true,
    critico:  item => item.stock_actual > 0 && item.stock_actual <= item.stock_minimo * 0.5,
    bajo:     item => item.stock_actual > item.stock_minimo * 0.5 && item.stock_actual <= item.stock_minimo,
    normal:   item => item.stock_actual > item.stock_minimo,
    agotado:  item => item.stock_actual === 0,
  };

  const filteredInventory = inventory
    .filter(item =>
      item.nombre?.toLowerCase().includes(searchText.toLowerCase()) ||
      item.sku?.toLowerCase().includes(searchText.toLowerCase())
    )
    .filter(filterMap[filterType] || (() => true));

  const filters = [
    { key: 'todos',   label: 'Todos',    color: '#3498db' },
    { key: 'critico', label: 'Crítico',  color: '#e74c3c' },
    { key: 'bajo',    label: 'Bajo',     color: '#f39c12' },
    { key: 'normal',  label: 'Normal',   color: '#2ecc71' },
    { key: 'agotado', label: 'Agotado',  color: '#95a5a6' },
  ];

  const renderHeader = () => (
    <View>
      {/* Búsqueda */}
      <View style={styles.searchContainer}>
        <MaterialIcons name="search" size={20} color="#7f8c8d" style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Buscar por nombre o SKU"
          value={searchText}
          onChangeText={setSearchText}
        />
        {searchText !== '' && (
          <TouchableOpacity onPress={() => setSearchText('')}>
            <MaterialIcons name="clear" size={20} color="#7f8c8d" />
          </TouchableOpacity>
        )}
      </View>

      {/* Filtros */}
      <View style={styles.filterContainer}>
        {filters.map(f => (
          <TouchableOpacity
            key={f.key}
            style={[
              styles.filterChip,
              { borderColor: f.color },
              filterType === f.key && { backgroundColor: f.color },
            ]}
            onPress={() => setFilterType(f.key)}
          >
            <Text style={[
              styles.filterChipText,
              filterType === f.key && styles.filterChipTextActive,
            ]}>
              {f.label}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Contador + botón escanear */}
      <View style={styles.statsContainer}>
        <Text style={styles.statsText}>{filteredInventory.length} productos</Text>
        <TouchableOpacity style={styles.scanButton} onPress={() => navigation.navigate('Scan')}>
          <MaterialIcons name="qr-code-scanner" size={18} color="#3498db" />
          <Text style={styles.scanButtonText}>Escanear</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  const renderItem = ({ item }) => {
    const status = getStockStatus(item);
    // Barra de progreso: stock_actual sobre el doble del stock_minimo como referencia
    const maxRef = Math.max(item.stock_minimo * 2, item.stock_actual, 1);
    const progressPct = Math.min((item.stock_actual / maxRef) * 100, 100);

    return (
      <TouchableOpacity
        style={styles.productCard}
        onPress={() => navigation.navigate('ProductDetail', { product: item })}
      >
        <View style={styles.productHeader}>
          <View style={styles.productHeaderLeft}>
            <Text style={styles.productName} numberOfLines={1}>{item.nombre}</Text>
            <Text style={styles.productSku}>SKU: {item.sku}</Text>
          </View>
          <View style={[styles.stockBadge, { backgroundColor: status.color }]}>
            <Text style={styles.stockBadgeText}>{status.text}</Text>
          </View>
        </View>

        <View style={styles.productDetails}>
          {/* Stock actual */}
          <View style={styles.detailRow}>
            <MaterialIcons name="inventory" size={15} color="#7f8c8d" />
            <Text style={styles.detailText}>
              {' '}Stock: {item.stock_actual} unid. (mín. {item.stock_minimo})
            </Text>
          </View>

          {/* Precio */}
          <View style={styles.detailRow}>
            <MaterialIcons name="attach-money" size={15} color="#7f8c8d" />
            <Text style={styles.detailText}> ${parseFloat(item.precio_unitario).toFixed(2)}</Text>
          </View>

          {/* Estado */}
          <View style={styles.detailRow}>
            <MaterialIcons name="circle" size={15} color={status.color} />
            <Text style={styles.detailText}> {item.estado}</Text>
          </View>

          {/* Barra de progreso */}
          <View style={styles.progressBar}>
            <View
              style={[
                styles.progressFill,
                { width: `${progressPct}%`, backgroundColor: status.color }
              ]}
            />
          </View>

          <View style={styles.footerRow}>
            <Text style={styles.updateText}>ID: {item.id_producto}</Text>
            <Text style={styles.detailLink}>Ver detalle →</Text>
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Inventario</Text>
        <TouchableOpacity onPress={() => navigation.navigate('Home')}>
          <MaterialIcons name="home" size={24} color="#2c3e50" />
        </TouchableOpacity>
      </View>

      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#3498db" />
          <Text style={styles.loadingText}>Cargando inventario...</Text>
        </View>
      ) : (
        <FlatList
          data={filteredInventory}
          renderItem={renderItem}
          keyExtractor={item => item.id_producto.toString()}
          ListHeaderComponent={renderHeader}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <MaterialIcons name="inventory" size={60} color="#bdc3c7" />
              <Text style={styles.emptyText}>No se encontraron productos</Text>
              <Text style={styles.emptySubtext}>Intenta con otra búsqueda o filtro</Text>
            </View>
          }
        />
      )}
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  header: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', padding: 20,
    backgroundColor: 'white', borderBottomWidth: 1, borderBottomColor: '#ecf0f1',
  },
  title: { fontSize: 24, fontWeight: 'bold', color: '#2c3e50' },
  searchContainer: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: 'white', margin: 15, paddingHorizontal: 15,
    borderRadius: 10, borderWidth: 1, borderColor: '#bdc3c7',
  },
  searchIcon: { marginRight: 10 },
  searchInput: { flex: 1, paddingVertical: 12, fontSize: 15 },
  filterContainer: {
    flexDirection: 'row', paddingHorizontal: 15, marginBottom: 12, flexWrap: 'wrap',
  },
  filterChip: {
    paddingHorizontal: 12, paddingVertical: 6, borderRadius: 20,
    borderWidth: 1.5, marginRight: 8, marginBottom: 6, backgroundColor: 'white',
  },
  filterChipText: { fontSize: 12, color: '#7f8c8d' },
  filterChipTextActive: { color: 'white', fontWeight: 'bold' },
  statsContainer: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', paddingHorizontal: 15, marginBottom: 10,
  },
  statsText: { color: '#7f8c8d', fontSize: 14 },
  scanButton: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#ebf5ff', paddingHorizontal: 12,
    paddingVertical: 6, borderRadius: 15,
  },
  scanButtonText: { color: '#3498db', marginLeft: 4, fontSize: 12, fontWeight: '600' },
  listContainer: { padding: 15, paddingTop: 0 },
  productCard: {
    backgroundColor: 'white', borderRadius: 10, padding: 15, marginBottom: 10, elevation: 2,
  },
  productHeader: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'flex-start', marginBottom: 10,
  },
  productHeaderLeft: { flex: 1 },
  productName: { fontSize: 16, fontWeight: 'bold', color: '#2c3e50' },
  productSku: { fontSize: 12, color: '#7f8c8d', marginTop: 2 },
  stockBadge: {
    paddingHorizontal: 8, paddingVertical: 3, borderRadius: 12, marginLeft: 10,
  },
  stockBadgeText: { color: 'white', fontSize: 10, fontWeight: 'bold' },
  productDetails: { marginTop: 5 },
  detailRow: { flexDirection: 'row', alignItems: 'center', marginVertical: 2 },
  detailText: { color: '#34495e', fontSize: 13 },
  progressBar: {
    height: 4, backgroundColor: '#ecf0f1',
    borderRadius: 2, marginVertical: 8,
  },
  progressFill: { height: '100%', borderRadius: 2 },
  footerRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', marginTop: 4,
  },
  updateText: { fontSize: 11, color: '#95a5a6' },
  detailLink: { fontSize: 12, color: '#3498db', fontWeight: '600' },
  loadingContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingText: { marginTop: 10, color: '#7f8c8d' },
  emptyContainer: { alignItems: 'center', justifyContent: 'center', padding: 40 },
  emptyText: { fontSize: 18, color: '#7f8c8d', marginTop: 10 },
  emptySubtext: { fontSize: 14, color: '#95a5a6', marginTop: 5 },
});

export default InventoryScreen;