// screens/NotificacionesScreen.js
import React, { useState, useEffect } from 'react';
import {
  View, Text, StyleSheet, FlatList,
  TouchableOpacity, SafeAreaView
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import AsyncStorage from '@react-native-async-storage/async-storage';

const NOTIFICACIONES_DEFAULT = [
  {
    id: '1', tipo: 'stock', titulo: 'Alerta de Stock Bajo',
    mensaje: 'Filtro de Aceite Hidráulico (HOF-500-M) se está agotando. Cantidad actual: 23',
    tiempo: 'hace 4 días', leida: false, icon: 'warning-outline', color: '#f59e0b',
  },
  {
    id: '2', tipo: 'error', titulo: 'Error de Validación',
    mensaje: 'Se detectó discrepancia de ubicación para SKU ISG-2024-XL en sección A-12',
    tiempo: 'hace 4 días', leida: false, icon: 'alert-circle-outline', color: '#ef4444',
  },
  {
    id: '3', tipo: 'sistema', titulo: 'Mantenimiento del Sistema',
    mensaje: 'Mantenimiento programado esta noche a las 11 PM EST',
    tiempo: 'hace 4 días', leida: true, icon: 'construct-outline', color: '#3b82f6',
  },
  {
    id: '4', tipo: 'exito', titulo: 'Tarea de Picking Completada',
    mensaje: 'La tarea PT-005 (Film Estirable para Pallets) se completó exitosamente',
    tiempo: 'hace 4 días', leida: true, icon: 'checkmark-circle-outline', color: '#10b981',
  },
];

const NotificacionesScreen = () => {
  const insets = useSafeAreaInsets();
  const [notificaciones, setNotificaciones] = useState([]);

  const noLeidasCount = notificaciones.filter(n => !n.leida).length;

  // Cargar notificaciones desde AsyncStorage al montar
  useEffect(() => {
    const cargar = async () => {
      try {
        const raw = await AsyncStorage.getItem('notificaciones');
        if (raw) {
          setNotificaciones(JSON.parse(raw));
        } else {
          setNotificaciones(NOTIFICACIONES_DEFAULT);
          await AsyncStorage.setItem('notificaciones', JSON.stringify(NOTIFICACIONES_DEFAULT));
        }
      } catch {
        setNotificaciones(NOTIFICACIONES_DEFAULT);
      }
    };
    cargar();
  }, []);

  // Guardar en AsyncStorage cada vez que cambien
  const guardarYActualizar = async (nuevas) => {
    setNotificaciones(nuevas);
    try {
      await AsyncStorage.setItem('notificaciones', JSON.stringify(nuevas));
    } catch {}
  };

  const marcarTodasLeidas = () => {
    const nuevas = notificaciones.map(n => ({ ...n, leida: true }));
    guardarYActualizar(nuevas);
  };

  const marcarLeida = (id) => {
    const nuevas = notificaciones.map(n => n.id === id ? { ...n, leida: true } : n);
    guardarYActualizar(nuevas);
  };

  const renderNotificacion = ({ item }) => (
    <TouchableOpacity
      style={[styles.notificacionCard, !item.leida && styles.noLeida]}
      onPress={() => marcarLeida(item.id)}
    >
      <View style={[styles.iconContainer, { backgroundColor: `${item.color}22` }]}>
        <Ionicons name={item.icon} size={24} color={item.color} />
      </View>
      <View style={styles.contentContainer}>
        <View style={styles.headerRow}>
          <Text style={styles.titulo} numberOfLines={1}>{item.titulo}</Text>
          {!item.leida && <View style={styles.puntoNoLeido} />}
        </View>
        <Text style={styles.mensaje} numberOfLines={2}>{item.mensaje}</Text>
        <Text style={styles.tiempo}>{item.tiempo}</Text>
      </View>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
        <Text style={styles.headerTitle}>Notificaciones</Text>
        <View style={styles.headerRight}>
          <Text style={styles.noLeidasCount}>
            {noLeidasCount > 0 ? `${noLeidasCount} sin leer` : 'Todas leídas'}
          </Text>
          {noLeidasCount > 0 && (
            <TouchableOpacity onPress={marcarTodasLeidas}>
              <Text style={styles.marcarTodas}>Marcar todas</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>

      <FlatList
        data={notificaciones}
        renderItem={renderNotificacion}
        keyExtractor={item => item.id}
        contentContainerStyle={styles.listContainer}
        showsVerticalScrollIndicator={false}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="notifications-off-outline" size={50} color="#bdc3c7" />
            <Text style={styles.emptyText}>Sin notificaciones</Text>
          </View>
        }
      />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  header: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', padding: 20,
    backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#e5e7eb',
  },
  headerTitle: { fontSize: 20, fontWeight: 'bold', color: '#1f2937' },
  headerRight: { alignItems: 'flex-end' },
  noLeidasCount: { fontSize: 14, color: '#2563eb', fontWeight: '500' },
  marcarTodas: { fontSize: 12, color: '#9ca3af', marginTop: 4 },
  listContainer: { padding: 16 },
  notificacionCard: {
    flexDirection: 'row', backgroundColor: '#fff',
    borderRadius: 12, padding: 16, marginBottom: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  noLeida: {
    backgroundColor: '#f0f9ff', borderWidth: 1, borderColor: '#93c5fd',
  },
  iconContainer: {
    width: 48, height: 48, borderRadius: 24,
    justifyContent: 'center', alignItems: 'center', marginRight: 16,
  },
  contentContainer: { flex: 1 },
  headerRow: {
    flexDirection: 'row', justifyContent: 'space-between',
    alignItems: 'center', marginBottom: 4,
  },
  titulo: { fontSize: 15, fontWeight: '600', color: '#1f2937', flex: 1 },
  puntoNoLeido: {
    width: 10, height: 10, borderRadius: 5,
    backgroundColor: '#2563eb', marginLeft: 8,
  },
  mensaje: { fontSize: 13, color: '#6b7280', marginBottom: 6, lineHeight: 19 },
  tiempo: { fontSize: 11, color: '#9ca3af' },
  emptyContainer: { alignItems: 'center', padding: 40 },
  emptyText: { fontSize: 16, color: '#7f8c8d', marginTop: 10 },
});

export default NotificacionesScreen;