// screens/ProfileScreen.js
import React, { useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, SafeAreaView, Alert, Image
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const LOGO    = require('../assets/logo.jpeg');
const API_URL = 'https://inventario-pi-1.onrender.com';

const PERMISOS_INFO = {
  inventario: { label: 'Ver Inventario',     icon: 'cube-outline',          color: '#2563eb', bg: '#eff6ff' },
  escanear:   { label: 'Escanear Códigos',   icon: 'scan-outline',          color: '#16a34a', bg: '#f0fdf4' },
  reportes:   { label: 'Reportar Problemas', icon: 'alert-circle-outline',  color: '#f59e0b', bg: '#fffbeb' },
  picking:    { label: 'Tareas de Picking',  icon: 'clipboard-outline',     color: '#8b5cf6', bg: '#f5f3ff' },
};

const ProfileScreen = ({ navigation }) => {
  const insets   = useSafeAreaInsets();
  const [userData, setUserData] = useState(null);
  const [permisos, setPermisos] = useState([]);
  const [cargandoPermisos, setCargandoPermisos] = useState(false);

  useFocusEffect(useCallback(() => {
    loadUser();
  }, []));

  const loadUser = async () => {
    try {
      const raw = await AsyncStorage.getItem('currentUser');
      if (raw) {
        const user = JSON.parse(raw);
        setUserData(user);
        // Cargar permisos frescos desde la API
        await cargarPermisosDesdeAPI(user.id_empleado);
      }
    } catch {}
  };

  const cargarPermisosDesdeAPI = async (idEmpleado) => {
    if (!idEmpleado) return;
    setCargandoPermisos(true);
    try {
      const resp = await fetch(`${API_URL}/v1/usuarios/`);
      const data = await resp.json();
      const usuarios = data.usuarios ?? [];
      const yo = usuarios.find(u => u.id_empleado === idEmpleado);
      if (yo) {
        const p = (yo.permisos || '').split(',').filter(Boolean);
        setPermisos(p);
        // Actualizar sesión en AsyncStorage con permisos frescos
        const raw = await AsyncStorage.getItem('currentUser');
        if (raw) {
          const user = JSON.parse(raw);
          user.permisos = p;
          user.rol = yo.rol;
          await AsyncStorage.setItem('currentUser', JSON.stringify(user));
          setUserData(user);
        }
      }
    } catch {} finally {
      setCargandoPermisos(false);
    }
  };

  const handleLogout = () => {
    Alert.alert('Cerrar Sesión', '¿Estás seguro de que deseas cerrar sesión?', [
      { text: 'Cancelar', style: 'cancel' },
      {
        text: 'Cerrar Sesión', style: 'destructive',
        onPress: async () => {
          await AsyncStorage.removeItem('currentUser');
          await AsyncStorage.removeItem('userSession');
          navigation.reset({ index: 0, routes: [{ name: 'Login' }] });
        },
      },
    ]);
  };

  const initials = userData?.nombre
    ? userData.nombre.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
    : '?';

  const InfoRow = ({ icon, label, value }) => (
    <View style={styles.infoRow}>
      <View style={styles.infoIconBox}>
        <Ionicons name={icon} size={18} color="#3b82f6" />
      </View>
      <View style={styles.infoText}>
        <Text style={styles.infoLabel}>{label}</Text>
        <Text style={styles.infoValue}>{value || '—'}</Text>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false}>

        {/* HEADER */}
        <View style={[styles.header, { paddingTop: insets.top + 16 }]}>
          <TouchableOpacity style={[styles.backBtn, { top: insets.top + 16 }]} onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </TouchableOpacity>
          <Image source={LOGO} style={[styles.headerLogo, { top: insets.top + 12 }]} resizeMode="contain" />
          <Text style={styles.headerTitle}>Perfil</Text>
          <View style={styles.avatarWrap}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>{initials}</Text>
            </View>
          </View>
          <Text style={styles.userName}>{userData?.nombre || 'Usuario'}</Text>
          <Text style={styles.userRole}>{userData?.rol || 'Operador'}</Text>
        </View>

        <View style={styles.content}>

          {/* Info de cuenta */}
          <Text style={styles.sectionTitle}>Información de Cuenta</Text>
          <View style={styles.card}>
            <InfoRow icon="person-outline"   label="Nombre"         value={userData?.nombre} />
            <View style={styles.sep} />
            <InfoRow icon="id-card-outline"  label="ID de Empleado" value={userData?.id_empleado} />
            <View style={styles.sep} />
            <InfoRow icon="mail-outline"     label="Correo"         value={userData?.email} />
            <View style={styles.sep} />
            <InfoRow icon="shield-outline"   label="Rol"            value={userData?.rol || 'Operador'} />
          </View>

          {/* Permisos asignados */}
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Permisos Asignados</Text>
            <TouchableOpacity onPress={() => cargarPermisosDesdeAPI(userData?.id_empleado)} style={styles.refreshBtn}>
              <Ionicons name="refresh-outline" size={16} color="#2563eb" />
              <Text style={styles.refreshText}>Actualizar</Text>
            </TouchableOpacity>
          </View>

          {cargandoPermisos ? (
            <View style={styles.card}>
              <Text style={styles.cargandoText}>Cargando permisos...</Text>
            </View>
          ) : permisos.length > 0 ? (
            <View style={styles.card}>
              {Object.entries(PERMISOS_INFO).map(([key, info], i, arr) => {
                const tiene = permisos.includes(key);
                return (
                  <View key={key}>
                    <View style={styles.permisoRow}>
                      <View style={[styles.permisoIcon, { backgroundColor: tiene ? info.bg : '#f1f5f9' }]}>
                        <Ionicons name={info.icon} size={20} color={tiene ? info.color : '#94a3b8'} />
                      </View>
                      <Text style={[styles.permisoLabel, !tiene && { color: '#94a3b8' }]}>{info.label}</Text>
                      <View style={[styles.permisoBadge, { backgroundColor: tiene ? '#dcfce7' : '#f1f5f9' }]}>
                        <Ionicons
                          name={tiene ? 'checkmark-circle' : 'close-circle'}
                          size={16}
                          color={tiene ? '#16a34a' : '#94a3b8'}
                        />
                        <Text style={[styles.permisoBadgeText, { color: tiene ? '#16a34a' : '#94a3b8' }]}>
                          {tiene ? 'Activo' : 'Sin acceso'}
                        </Text>
                      </View>
                    </View>
                    {i < arr.length - 1 && <View style={styles.sep} />}
                  </View>
                );
              })}
            </View>
          ) : (
            <View style={[styles.card, { alignItems: 'center', padding: 24 }]}>
              <Ionicons name="lock-closed-outline" size={36} color="#94a3b8" />
              <Text style={{ fontSize: 14, color: '#64748b', marginTop: 10, textAlign: 'center', lineHeight: 20 }}>
                Aún no tienes permisos asignados.{'\n'}
                Contacta al administrador.
              </Text>
            </View>
          )}

          {/* Cerrar sesión */}
          <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout} activeOpacity={0.85}>
            <Ionicons name="log-out-outline" size={20} color="#ffffff" />
            <Text style={styles.logoutText}>Cerrar Sesión</Text>
          </TouchableOpacity>

          <Text style={styles.footer}>Universal Inventory v2.4.1{'\n'}© 2026 Todos los derechos reservados</Text>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f1f5f9' },
  header: {
    backgroundColor: '#1e2d4a', paddingTop: 16, paddingBottom: 32,
    alignItems: 'center', paddingHorizontal: 20,
  },
  backBtn: {
    position: 'absolute', top: 16, left: 16,
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center', alignItems: 'center',
  },
  headerLogo: { position: 'absolute', top: 12, right: 16, width: 48, height: 48, opacity: 0.6 },
  headerTitle: { fontSize: 17, fontWeight: '700', color: '#ffffff', marginBottom: 20 },
  avatarWrap: {
    width: 80, height: 80, borderRadius: 40,
    borderWidth: 3, borderColor: '#3b82f6',
    justifyContent: 'center', alignItems: 'center', marginBottom: 12,
  },
  avatar: {
    width: 70, height: 70, borderRadius: 35,
    backgroundColor: '#1e3a8a', justifyContent: 'center', alignItems: 'center',
  },
  avatarText: { fontSize: 26, fontWeight: '800', color: '#ffffff' },
  userName:   { fontSize: 18, fontWeight: '700', color: '#ffffff', marginBottom: 4 },
  userRole:   { fontSize: 13, color: 'rgba(255,255,255,0.6)' },

  content: { padding: 16 },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 10, marginTop: 8 },
  sectionTitle: { fontSize: 15, fontWeight: '700', color: '#1e2d4a' },
  refreshBtn: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  refreshText: { fontSize: 12, color: '#2563eb', fontWeight: '600' },

  card: {
    backgroundColor: '#ffffff', borderRadius: 14, paddingHorizontal: 16, marginBottom: 16,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06, shadowRadius: 8, elevation: 2,
  },
  sep: { height: 1, backgroundColor: '#f1f5f9' },

  infoRow:    { flexDirection: 'row', alignItems: 'center', paddingVertical: 14 },
  infoIconBox: {
    width: 36, height: 36, borderRadius: 8,
    backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginRight: 12,
  },
  infoText:  { flex: 1 },
  infoLabel: { fontSize: 11, color: '#94a3b8', fontWeight: '500', marginBottom: 2 },
  infoValue: { fontSize: 14, color: '#1e293b', fontWeight: '600' },

  permisoRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14 },
  permisoIcon: {
    width: 36, height: 36, borderRadius: 8,
    justifyContent: 'center', alignItems: 'center', marginRight: 12,
  },
  permisoLabel: { flex: 1, fontSize: 14, color: '#1e293b', fontWeight: '600' },
  permisoBadge: {
    flexDirection: 'row', alignItems: 'center', gap: 4,
    borderRadius: 20, paddingVertical: 4, paddingHorizontal: 10,
  },
  permisoBadgeText: { fontSize: 12, fontWeight: '600' },
  cargandoText: { textAlign: 'center', color: '#94a3b8', padding: 20, fontSize: 13 },

  logoutBtn: {
    flexDirection: 'row', backgroundColor: '#ef4444',
    borderRadius: 12, paddingVertical: 15,
    justifyContent: 'center', alignItems: 'center', gap: 8, marginBottom: 20,
    shadowColor: '#ef4444', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 5,
  },
  logoutText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },
  footer: { textAlign: 'center', fontSize: 11, color: '#94a3b8', lineHeight: 18, marginBottom: 10 },
});

export default ProfileScreen;