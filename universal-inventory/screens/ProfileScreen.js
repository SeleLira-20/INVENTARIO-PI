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

const LOGO = require('../assets/logo.jpeg');

const ProfileScreen = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [userData, setUserData] = useState(null);

  useFocusEffect(useCallback(() => { loadUser(); }, []));

  const loadUser = async () => {
    try {
      const raw = await AsyncStorage.getItem('currentUser');
      if (raw) setUserData(JSON.parse(raw));
    } catch {}
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

  const MenuItem = ({ icon, label, onPress }) => (
    <TouchableOpacity style={styles.menuItem} onPress={onPress} activeOpacity={0.7}>
      <View style={styles.menuIcon}>
        <Ionicons name={icon} size={18} color="#3b82f6" />
      </View>
      <Text style={styles.menuLabel}>{label}</Text>
      <Ionicons name="chevron-forward" size={16} color="#cbd5e1" />
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false}>

        {/* HEADER CON LOGO */}
        <View style={[styles.header, { paddingTop: insets.top + 16 }]}>
          <TouchableOpacity style={[styles.backBtn, { top: insets.top + 16 }]} onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={22} color="#fff" />
          </TouchableOpacity>

          {/* Logo arriba a la derecha semitransparente */}
          <Image source={LOGO} style={[styles.headerLogo, { top: insets.top + 12 }]} resizeMode="contain" />

          <Text style={styles.headerTitle}>Perfil</Text>

          <View style={styles.avatarWrap}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>{initials}</Text>
            </View>
          </View>
          <Text style={styles.userName}>{userData?.nombre || 'Usuario'}</Text>
          <Text style={styles.userRole}>Operador de Almacén</Text>
        </View>

        <View style={styles.content}>
          <Text style={styles.sectionTitle}>Información de Cuenta</Text>
          <View style={styles.card}>
            <InfoRow icon="person-outline"  label="Nombre"             value={userData?.nombre} />
            <View style={styles.sep} />
            <InfoRow icon="ellipse-outline" label="ID de Empleado"     value={userData?.idEmpleado} />
            <View style={styles.sep} />
            <InfoRow icon="mail-outline"    label="Correo Electrónico" value={userData?.email} />
            <View style={styles.sep} />
            <InfoRow icon="time-outline"    label="Turno"              value="Turno Diurno" />
            <View style={styles.sep} />
            <InfoRow icon="ellipse-outline" label="Rol"                value="Operador de Almacén" />
          </View>

          <Text style={styles.sectionTitle}>Preferencias</Text>
          <View style={styles.card}>
            <MenuItem icon="notifications-outline" label="Notificaciones" onPress={() => {}} />
            <View style={styles.sep} />
            <MenuItem icon="settings-outline" label="Configuración de App" onPress={() => {}} />
          </View>

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
  headerLogo: {
    position: 'absolute', top: 12, right: 16,
    width: 48, height: 48, opacity: 0.6,
  },
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
  userName: { fontSize: 18, fontWeight: '700', color: '#ffffff', marginBottom: 4 },
  userRole: { fontSize: 13, color: 'rgba(255,255,255,0.6)' },

  content: { padding: 16 },
  sectionTitle: { fontSize: 15, fontWeight: '700', color: '#1e2d4a', marginBottom: 10, marginTop: 8 },

  card: {
    backgroundColor: '#ffffff', borderRadius: 14, paddingHorizontal: 16, marginBottom: 16,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06, shadowRadius: 8, elevation: 2,
  },
  sep: { height: 1, backgroundColor: '#f1f5f9' },

  infoRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14 },
  infoIconBox: {
    width: 36, height: 36, borderRadius: 8,
    backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginRight: 12,
  },
  infoText: { flex: 1 },
  infoLabel: { fontSize: 11, color: '#94a3b8', fontWeight: '500', marginBottom: 2 },
  infoValue: { fontSize: 14, color: '#1e293b', fontWeight: '600' },

  menuItem: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14 },
  menuIcon: {
    width: 36, height: 36, borderRadius: 8,
    backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginRight: 12,
  },
  menuLabel: { flex: 1, fontSize: 14, color: '#1e293b', fontWeight: '500' },

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