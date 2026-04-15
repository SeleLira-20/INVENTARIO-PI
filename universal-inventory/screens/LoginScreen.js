// screens/LoginScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Alert, KeyboardAvoidingView, Platform, ScrollView, Image, ActivityIndicator
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LOGO = require('../assets/logo.jpeg');
const API_URL = 'http://192.168.100.99:8000';

const LoginScreen = ({ navigation, route }) => {
  const onLogin = route?.params?.onLogin;
  const [idEmpleado, setIdEmpleado] = useState('');
  const [pin, setPin]               = useState('');
  const [mostrarPin, setMostrarPin] = useState(false);
  const [cargando, setCargando]     = useState(false);

  const handleLogin = async () => {
    if (!idEmpleado.trim()) { Alert.alert('Error', 'Ingresa tu ID de empleado'); return; }
    if (!pin.trim())        { Alert.alert('Error', 'Ingresa tu PIN'); return; }
    if (!/^\d{4}$/.test(pin.trim())) { Alert.alert('Error', 'El PIN debe ser de 4 dígitos'); return; }

    setCargando(true);
    try {
      const resp = await fetch(`${API_URL}/v1/usuarios/login/pin`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id_empleado: idEmpleado.trim().toUpperCase(),
          pin: pin.trim(),
        }),
      });

      const data = await resp.json();

      if (data.status === '401' || !data.usuario) {
        Alert.alert('Acceso denegado', 'ID de empleado o PIN incorrectos.\n\nVerifica tus credenciales con el administrador.');
        return;
      }

      // Guardar sesión en AsyncStorage
      const sesion = {
        ...data.usuario,
        sesionActiva: true,
        fechaLogin: new Date().toISOString(),
      };
      await AsyncStorage.setItem('currentUser', JSON.stringify(sesion));
      await AsyncStorage.setItem('userSession', JSON.stringify(sesion));

      if (onLogin) onLogin(data.usuario.permisos ?? []);
      navigation.reset({ index: 0, routes: [{ name: 'MainTabs' }] });

    } catch (err) {
      Alert.alert(
        'Error de conexión',
        'No se pudo conectar con el servidor.\n\nVerifica que estés en la misma red WiFi.',
        [{ text: 'Reintentar', onPress: handleLogin }, { text: 'Cancelar', style: 'cancel' }]
      );
    } finally {
      setCargando(false);
    }
  };

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

        {/* HEADER */}
        <View style={styles.header}>
          <Image source={LOGO} style={styles.logo} resizeMode="contain" />
          <Text style={styles.appName}>Universal Inventory</Text>
          <Text style={styles.appSub}>Operaciones de Almacén</Text>
        </View>

        {/* FORM */}
        <View style={styles.form}>

          <View style={styles.infoBanner}>
            <Ionicons name="information-circle-outline" size={16} color="#2563eb" />
            <Text style={styles.infoBannerText}>
              Ingresa las credenciales asignadas por tu administrador
            </Text>
          </View>

          <Text style={styles.label}>ID de Empleado</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="person-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ej: EMP-001"
              placeholderTextColor="#94a3b8"
              value={idEmpleado}
              onChangeText={setIdEmpleado}
              autoCapitalize="characters"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>PIN (4 dígitos)</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="lock-closed-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="••••"
              placeholderTextColor="#94a3b8"
              value={pin}
              onChangeText={setPin}
              secureTextEntry={!mostrarPin}
              keyboardType="numeric"
              maxLength={4}
              autoCorrect={false}
            />
            <TouchableOpacity onPress={() => setMostrarPin(!mostrarPin)} style={styles.eyeBtn}>
              <Ionicons name={mostrarPin ? 'eye-off-outline' : 'eye-outline'} size={18} color="#64748b" />
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            style={[styles.btn, cargando && { opacity: 0.7 }]}
            onPress={handleLogin}
            disabled={cargando}
            activeOpacity={0.85}
          >
            {cargando
              ? <ActivityIndicator color="white" />
              : <Text style={styles.btnText}>Iniciar Sesión  →</Text>
            }
          </TouchableOpacity>

          <View style={styles.helpBox}>
            <Ionicons name="help-circle-outline" size={16} color="#94a3b8" />
            <Text style={styles.helpText}>
              ¿No tienes credenciales? Contacta a tu administrador para que te asigne un PIN.
            </Text>
          </View>

        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#1e2d4a' },
  scroll: { flexGrow: 1 },

  header: {
    backgroundColor: '#1e2d4a',
    paddingTop: 60, paddingBottom: 32,
    alignItems: 'center', paddingHorizontal: 24,
  },
  logo:    { width: 130, height: 130, marginBottom: 12 },
  appName: { fontSize: 22, fontWeight: '800', color: '#ffffff', letterSpacing: 0.3 },
  appSub:  { fontSize: 13, color: 'rgba(255,255,255,0.6)', marginTop: 4 },

  form: {
    flex: 1, backgroundColor: '#ffffff',
    borderTopLeftRadius: 28, borderTopRightRadius: 28,
    paddingHorizontal: 24, paddingTop: 28, paddingBottom: 40,
  },

  infoBanner: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    backgroundColor: '#eff6ff', borderRadius: 10, padding: 12,
    marginBottom: 20, borderWidth: 1, borderColor: '#bfdbfe',
  },
  infoBannerText: { flex: 1, fontSize: 12, color: '#1e40af', lineHeight: 17 },

  label: { fontSize: 13, fontWeight: '600', color: '#1e2d4a', marginBottom: 8, marginTop: 16 },
  inputWrapper: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 10,
    borderWidth: 1.5, borderColor: '#e2e8f0', paddingHorizontal: 12,
  },
  inputIcon: { marginRight: 8 },
  input: {
    flex: 1, fontSize: 15, color: '#1e293b',
    paddingVertical: Platform.OS === 'ios' ? 14 : 10,
  },
  eyeBtn: { paddingLeft: 8 },

  btn: {
    backgroundColor: '#1e3a8a', borderRadius: 12, paddingVertical: 16,
    alignItems: 'center', marginTop: 28, marginBottom: 20,
    shadowColor: '#1e3a8a', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.4, shadowRadius: 10, elevation: 6,
  },
  btnText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },

  helpBox: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    backgroundColor: '#f8fafc', borderRadius: 10, padding: 12,
    borderWidth: 1, borderColor: '#e2e8f0',
  },
  helpText: { flex: 1, fontSize: 12, color: '#64748b', lineHeight: 17 },
});

export default LoginScreen;