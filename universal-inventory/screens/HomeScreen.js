// screens/HomeScreen.js
import React, { useState, useCallback, useContext } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, SafeAreaView, Image
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { PermisosContext } from '../App';

const LOGO    = require('../assets/logo.jpeg');
const API_BASE = 'https://inventario-pi-1.onrender.com';

const HomeScreen = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const { permisos, setPermisos } = useContext(PermisosContext);
  const [userData,      setUserData]      = useState({ nombre: 'Usuario', id_empleado: '' });
  const [noLeidasCount, setNoLeidasCount] = useState(0);
  const [refreshing, setRefreshing]     = useState(false);

  useFocusEffect(useCallback(() => {
    loadUserData();
    loadNoLeidas();
  }, []));

  const loadUserData = async () => {
    try {
      const raw = await AsyncStorage.getItem('currentUser');
      if (!raw) return;
      const user = JSON.parse(raw);
      setUserData(user);
      // Consultar permisos frescos desde la API en tiempo real
      await actualizarPermisos(user.id_empleado);
    } catch {}
  };

  const actualizarPermisos = async (idEmpleado) => {
    if (!idEmpleado) return;
    try {
      const resp = await fetch(`${API_URL}/v1/usuarios/`);
      const data = await resp.json();
      const yo   = (data.usuarios ?? []).find(u => u.id_empleado === idEmpleado);
      if (yo) {
        const nuevosPermisos = (yo.permisos || '').split(',').map(x => x.trim().toLowerCase()).filter(Boolean);
        setPermisos(nuevosPermisos);
        // Actualizar AsyncStorage con permisos frescos
        const raw  = await AsyncStorage.getItem('currentUser');
        if (raw) {
          const user = JSON.parse(raw);
          user.permisos = nuevosPermisos;
          user.rol      = yo.rol;
          await AsyncStorage.setItem('currentUser', JSON.stringify(user));
          setUserData(prev => ({ ...prev, rol: yo.rol }));
        }
      }
    } catch {}
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await loadUserData();
    await loadNoLeidas();
    setRefreshing(false);
  };

  const loadNoLeidas = async () => {
    try {
      const raw = await AsyncStorage.getItem('notificaciones');
      if (raw) {
        const lista = JSON.parse(raw);
        setNoLeidasCount(lista.filter(n => !n.leida).length);
      }
    } catch {}
  };

  const todasLasAcciones = [
    { key: 'escanear',   permiso: 'escanear',   label: 'Escanear\nCódigo',   icon: 'scan-outline',          color: '#22c55e', navTarget: 'Scan'      },
    { key: 'inventario', permiso: 'inventario',  label: 'Ver Inventario',     icon: 'cube-outline',          color: '#1d4ed8', navTarget: 'Inventory' },
    { key: 'picking',    permiso: 'picking',     label: 'Tareas de\nPicking', icon: 'clipboard-outline',     color: '#f59e0b', navTarget: 'Picking'   },
    { key: 'reportes',   permiso: 'reportes',    label: 'Reportar\nProblema', icon: 'alert-circle-outline',  color: '#ef4444', navTarget: 'Reports'   },
  ];

  const accionesVisibles = todasLasAcciones.filter(a => permisos.includes(a.permiso));

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false}>

        {/* HEADER */}
        <View style={[styles.header, { paddingTop: insets.top + 18 }]}>
          <View style={styles.headerTop}>
            <View style={styles.headerLeft}>
              <Image source={LOGO} style={styles.headerLogo} resizeMode="contain" />
              <View>
                <Text style={styles.bienvenidoLabel}>Bienvenido</Text>
                <Text style={styles.nombreText}>{userData.nombre || 'Usuario'}</Text>
              </View>
            </View>
            <View style={styles.headerIcons}>
              <TouchableOpacity style={styles.iconButton} onPress={() => navigation.navigate('Notifications')}>
                <Ionicons name="notifications-outline" size={20} color="#fff" />
                {noLeidasCount > 0 && (
                  <View style={styles.badge}>
                    <Text style={styles.badgeText}>{noLeidasCount}</Text>
                  </View>
                )}
              </TouchableOpacity>
              <TouchableOpacity style={styles.iconButton} onPress={handleRefresh}>
                <Ionicons name={refreshing ? 'sync' : 'refresh-outline'} size={20} color="#fff" />
              </TouchableOpacity>
              <TouchableOpacity style={styles.iconButton} onPress={() => navigation.navigate('Profile')}>
                <Ionicons name="person-outline" size={20} color="#fff" />
              </TouchableOpacity>
            </View>
          </View>

          <View style={styles.infoCard}>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Rol</Text>
              <Text style={styles.infoValue}>{userData.rol || 'Operador'}</Text>
            </View>
            <View style={styles.infoDivider} />
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>ID de Empleado</Text>
              <Text style={styles.infoValue}>{userData.id_empleado || '—'}</Text>
            </View>
          </View>
        </View>

        <View style={styles.content}>
          <Text style={styles.sectionTitle}>Acciones Rápidas</Text>

          {accionesVisibles.length === 0 ? (
            <View style={styles.sinPermisosCard}>
              <Ionicons name="lock-closed-outline" size={36} color="#94a3b8" />
              <Text style={styles.sinPermisosText}>
                No tienes permisos asignados aún.{'\n'}Contacta al administrador.
              </Text>
            </View>
          ) : (
            <View style={styles.grid}>
              {accionesVisibles.map(accion => (
                <TouchableOpacity
                  key={accion.key}
                  style={[styles.card, { backgroundColor: accion.color }]}
                  onPress={() => navigation.navigate(accion.navTarget)}
                  activeOpacity={0.85}
                >
                  <View style={styles.cardIconCircle}>
                    <Ionicons name={accion.icon} size={32} color="#fff" />
                  </View>
                  <Text style={styles.cardText}>{accion.label}</Text>
                </TouchableOpacity>
              ))}
            </View>
          )}

          <Text style={styles.sectionTitle}>Resumen</Text>
          <View style={styles.resumenCard}>
            {[
              { numero: permisos.length,          label: 'Permisos',  color: '#2563eb' },
              { numero: noLeidasCount,             label: 'Notif.',    color: '#f59e0b' },
              { numero: accionesVisibles.length,   label: 'Accesos',   color: '#16a34a' },
            ].map((item, i, arr) => (
              <React.Fragment key={item.label}>
                <View style={styles.resumenItem}>
                  <Text style={[styles.resumenNumero, { color: item.color }]}>{item.numero}</Text>
                  <Text style={styles.resumenLabel}>{item.label}</Text>
                </View>
                {i < arr.length - 1 && <View style={styles.resumenDivider} />}
              </React.Fragment>
            ))}
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f3f4f6' },
  header: { backgroundColor: '#1e293b', paddingHorizontal: 20, paddingTop: 18, paddingBottom: 24 },
  headerTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 18 },
  headerLeft: { flexDirection: 'row', alignItems: 'center', gap: 10 },
  headerLogo: { width: 44, height: 44, borderRadius: 8 },
  bienvenidoLabel: { fontSize: 13, color: 'rgba(255,255,255,0.7)', fontWeight: '500' },
  nombreText: { fontSize: 18, fontWeight: '700', color: '#ffffff' },
  headerIcons: { flexDirection: 'row', gap: 10 },
  iconButton: {
    width: 38, height: 38, borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center', alignItems: 'center',
  },
  badge: {
    position: 'absolute', top: 4, right: 4,
    backgroundColor: '#ef4444', borderRadius: 6,
    minWidth: 13, height: 13, justifyContent: 'center', alignItems: 'center',
  },
  badgeText: { color: '#fff', fontSize: 8, fontWeight: 'bold' },
  infoCard: {
    backgroundColor: 'rgba(255,255,255,0.1)', borderRadius: 12,
    paddingVertical: 12, paddingHorizontal: 20,
    flexDirection: 'row', alignItems: 'center',
  },
  infoItem:    { flex: 1 },
  infoLabel:   { fontSize: 11, color: 'rgba(255,255,255,0.6)', marginBottom: 2 },
  infoValue:   { fontSize: 13, fontWeight: '700', color: '#ffffff' },
  infoDivider: { width: 1, height: 30, backgroundColor: 'rgba(255,255,255,0.2)', marginHorizontal: 16 },
  content:      { padding: 20 },
  sectionTitle: { fontSize: 17, fontWeight: '700', color: '#1e293b', marginBottom: 14, marginTop: 4 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12, marginBottom: 24 },
  card: {
    width: '47%', aspectRatio: 1.15, borderRadius: 16,
    justifyContent: 'center', alignItems: 'center', paddingVertical: 18,
    shadowColor: '#000', shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.15, shadowRadius: 8, elevation: 4,
  },
  cardIconCircle: {
    width: 56, height: 56, borderRadius: 28,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center', marginBottom: 10,
  },
  cardText: { color: '#ffffff', fontSize: 14, fontWeight: '700', textAlign: 'center', lineHeight: 19 },
  sinPermisosCard: {
    backgroundColor: 'white', borderRadius: 16, padding: 32,
    alignItems: 'center', marginBottom: 24,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07, shadowRadius: 8, elevation: 3,
  },
  sinPermisosText: { fontSize: 14, color: '#64748b', textAlign: 'center', marginTop: 12, lineHeight: 22 },
  resumenCard: {
    flexDirection: 'row', backgroundColor: '#ffffff', borderRadius: 16, paddingVertical: 22,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07, shadowRadius: 8, elevation: 3,
  },
  resumenItem:    { flex: 1, alignItems: 'center' },
  resumenNumero:  { fontSize: 36, fontWeight: '800' },
  resumenLabel:   { fontSize: 12, color: '#6b7280', marginTop: 4, fontWeight: '500' },
  resumenDivider: { width: 1, backgroundColor: '#e5e7eb', marginVertical: 6 },
});

export default HomeScreen;