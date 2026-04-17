import React, { useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  Alert, Modal, TextInput, ActivityIndicator, RefreshControl
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = 'http://192.168.100.99:8000';

const getPrioridadColor = p => ({ ALTA: '#e74c3c', MEDIA: '#f39c12', BAJA: '#3498db' }[p] || '#95a5a6');

const getEstadoColor = e => ({
  Pendiente:   '#3498db',
  'En Proceso': '#f39c12',
  Completada:  '#2ecc71',
  Cancelada:   '#e74c3c',
}[e] || '#95a5a6');

const PickingScreen = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [ordenes,          setOrdenes]          = useState([]);
  const [loading,          setLoading]          = useState(true);
  const [refreshing,       setRefreshing]       = useState(false);
  const [filtro,           setFiltro]           = useState('all');
  const [selectedOrden,    setSelectedOrden]    = useState(null);
  const [modalVisible,     setModalVisible]     = useState(false);
  const [cantidad,         setCantidad]         = useState('');
  const [guardando,        setGuardando]        = useState(false);
  const [usuarioId,        setUsuarioId]        = useState(1);

  // Cargar usuario actual
  useFocusEffect(useCallback(() => {
    cargarUsuario();
    cargarOrdenes();
  }, []));

  const cargarUsuario = async () => {
    try {
      const raw = await AsyncStorage.getItem('currentUser');
      if (raw) {
        const user = JSON.parse(raw);
        setUsuarioId(user.id_usuario ?? 1);
      }
    } catch {}
  };

  const cargarOrdenes = async () => {
    try {
      setLoading(true);
      const resp = await fetch(`${API_URL}/v1/picking/`);
      const data = await resp.json();
      setOrdenes(data.ordenes ?? []);
    } catch {
      Alert.alert('Error', 'No se pudo conectar con el servidor.');
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await cargarOrdenes();
    setRefreshing(false);
  };

  const cambiarEstado = async (id, nuevoEstado) => {
    try {
      const resp = await fetch(`${API_URL}/v1/picking/${id}/estado`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado: nuevoEstado }),
      });
      if (!resp.ok) throw new Error('Error al actualizar');
      await cargarOrdenes();
    } catch {
      Alert.alert('Error', 'No se pudo actualizar el estado.');
    }
  };

  const ordenesFiltradas = filtro === 'all'
    ? ordenes
    : ordenes.filter(o => o.estado === filtro);

  const filtros = [
    { key: 'all',        label: 'Todas',      color: '#64748b' },
    { key: 'Pendiente',  label: 'Pendiente',  color: '#3498db' },
    { key: 'En Proceso', label: 'En Proceso', color: '#f39c12' },
    { key: 'Completada', label: 'Completada', color: '#2ecc71' },
  ];

  const renderOrden = ({ item }) => (
    <View style={styles.taskCard}>
      <View style={styles.taskHeader}>
        <Text style={styles.taskId}>{item.numero_orden}</Text>
        <View style={[styles.statusBadge, { backgroundColor: getEstadoColor(item.estado) }]}>
          <Text style={styles.badgeText}>{item.estado}</Text>
        </View>
      </View>

      <View style={styles.infoRow}>
        <MaterialIcons name="person" size={14} color="#94a3b8" />
        <Text style={styles.infoText}> Usuario #{item.id_usuario_asignado}</Text>
      </View>

      <View style={styles.infoRow}>
        <MaterialIcons name="event" size={14} color="#94a3b8" />
        <Text style={styles.infoText}>
          {' '}{item.fecha_creacion
            ? new Date(item.fecha_creacion).toLocaleDateString('es-MX')
            : '—'}
        </Text>
      </View>

      {/* Botones de acción */}
      <View style={styles.accionesRow}>
        {item.estado === 'Pendiente' && (
          <TouchableOpacity
            style={[styles.accionBtn, { backgroundColor: '#f59e0b' }]}
            onPress={() => {
              Alert.alert('Iniciar orden', `¿Iniciar ${item.numero_orden}?`, [
                { text: 'Cancelar', style: 'cancel' },
                { text: 'Iniciar', onPress: () => cambiarEstado(item.id_orden, 'En Proceso') },
              ]);
            }}
          >
            <MaterialIcons name="play-arrow" size={16} color="white" />
            <Text style={styles.accionBtnText}>Iniciar</Text>
          </TouchableOpacity>
        )}

        {item.estado === 'En Proceso' && (
          <TouchableOpacity
            style={[styles.accionBtn, { backgroundColor: '#22c55e' }]}
            onPress={() => {
              Alert.alert('Completar orden', `¿Marcar ${item.numero_orden} como completada?`, [
                { text: 'Cancelar', style: 'cancel' },
                { text: 'Completar', onPress: () => cambiarEstado(item.id_orden, 'Completada') },
              ]);
            }}
          >
            <MaterialIcons name="check-circle" size={16} color="white" />
            <Text style={styles.accionBtnText}>Completar</Text>
          </TouchableOpacity>
        )}

        {item.estado !== 'Completada' && item.estado !== 'Cancelada' && (
          <TouchableOpacity
            style={[styles.accionBtn, { backgroundColor: '#ef4444' }]}
            onPress={() => {
              Alert.alert('Cancelar orden', `¿Cancelar ${item.numero_orden}?`, [
                { text: 'No', style: 'cancel' },
                { text: 'Cancelar orden', style: 'destructive', onPress: () => cambiarEstado(item.id_orden, 'Cancelada') },
              ]);
            }}
          >
            <MaterialIcons name="cancel" size={16} color="white" />
            <Text style={styles.accionBtnText}>Cancelar</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={[styles.header, { paddingTop: insets.top + 12 }]}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <MaterialIcons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
        <Text style={styles.title}>Picking</Text>
        <TouchableOpacity onPress={cargarOrdenes}>
          <MaterialIcons name="refresh" size={24} color="#1e293b" />
        </TouchableOpacity>
      </View>

      {/* Stats */}
      <View style={styles.statsRow}>
        <View style={styles.statCard}>
          <Text style={styles.statNum}>{ordenes.length}</Text>
          <Text style={styles.statLabel}>Total</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNum, { color: '#3498db' }]}>
            {ordenes.filter(o => o.estado === 'Pendiente').length}
          </Text>
          <Text style={styles.statLabel}>Pendientes</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNum, { color: '#f59e0b' }]}>
            {ordenes.filter(o => o.estado === 'En Proceso').length}
          </Text>
          <Text style={styles.statLabel}>En Proceso</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNum, { color: '#22c55e' }]}>
            {ordenes.filter(o => o.estado === 'Completada').length}
          </Text>
          <Text style={styles.statLabel}>Completadas</Text>
        </View>
      </View>

      {/* Filtros */}
      <View style={styles.filtrosRow}>
        {filtros.map(f => (
          <TouchableOpacity
            key={f.key}
            style={[styles.filtroChip, filtro === f.key && { backgroundColor: f.color, borderColor: f.color }]}
            onPress={() => setFiltro(f.key)}
          >
            <Text style={[styles.filtroText, filtro === f.key && { color: 'white' }]}>{f.label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#2563eb" />
          <Text style={styles.loadingText}>Cargando órdenes...</Text>
        </View>
      ) : (
        <FlatList
          data={ordenesFiltradas}
          renderItem={renderOrden}
          keyExtractor={item => item.id_orden.toString()}
          contentContainerStyle={{ padding: 15 }}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <MaterialIcons name="clipboard" size={60} color="#bdc3c7" />
              <Text style={styles.emptyText}>No hay órdenes {filtro !== 'all' ? filtro.toLowerCase()+'s' : ''}</Text>
            </View>
          }
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container:  { flex: 1, backgroundColor: '#f5f5f5' },
  header:     { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20, paddingVertical: 16, backgroundColor: '#2563eb' },
  refreshBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.2)', justifyContent: 'center', alignItems: 'center' },
  backBtn:    { width: 36, height: 36, borderRadius: 18, backgroundColor: 'rgba(255,255,255,0.2)', justifyContent: 'center', alignItems: 'center' },
  title:      { fontSize: 20, fontWeight: 'bold', color: 'white' },

  statsRow: { flexDirection: 'row', padding: 12, gap: 8 },
  statCard: { flex: 1, backgroundColor: 'white', borderRadius: 10, padding: 10, alignItems: 'center', elevation: 1 },
  statNum:  { fontSize: 22, fontWeight: '800', color: '#1e293b' },
  statLabel:{ fontSize: 10, color: '#94a3b8', marginTop: 2 },

  filtrosRow: { flexDirection: 'row', paddingHorizontal: 12, paddingBottom: 8, gap: 8, flexWrap: 'wrap' },
  filtroChip: { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 20, borderWidth: 1.5, borderColor: '#e2e8f0', backgroundColor: 'white' },
  filtroText: { fontSize: 12, fontWeight: '600', color: '#64748b' },

  taskCard: { backgroundColor: 'white', padding: 16, marginBottom: 12, borderRadius: 12, elevation: 2 },
  taskHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 10 },
  taskId:   { fontSize: 15, fontWeight: '800', color: '#1e3c72' },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
  badgeText:   { color: 'white', fontSize: 11, fontWeight: '700' },

  infoRow:  { flexDirection: 'row', alignItems: 'center', marginBottom: 4 },
  infoText: { fontSize: 13, color: '#64748b' },

  accionesRow: { flexDirection: 'row', gap: 8, marginTop: 12 },
  accionBtn:   { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', padding: 10, borderRadius: 8, gap: 6 },
  accionBtnText: { color: 'white', fontSize: 13, fontWeight: '700' },

  loadingContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingText:      { marginTop: 10, color: '#94a3b8', fontSize: 14 },
  emptyContainer:   { alignItems: 'center', padding: 40 },
  emptyText:        { fontSize: 16, color: '#94a3b8', marginTop: 10 },
});

export default PickingScreen;