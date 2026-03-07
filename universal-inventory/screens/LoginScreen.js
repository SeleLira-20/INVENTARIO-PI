// screens/LoginScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Alert, KeyboardAvoidingView, Platform, ScrollView, Image
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LOGO = require('../assets/logo.jpeg');

const LoginScreen = ({ navigation }) => {
  const [idEmpleado, setIdEmpleado] = useState('');
  const [pin, setPin] = useState('');
  const [mostrarPin, setMostrarPin] = useState(false);

  const handleLogin = async () => {
    if (!idEmpleado.trim()) { Alert.alert('Error', 'Ingresa tu ID de empleado'); return; }
    if (!pin.trim()) { Alert.alert('Error', 'Ingresa tu PIN'); return; }

    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch { usuarios = []; }

      const idNorm = idEmpleado.trim().toUpperCase();
      const usuario = usuarios.find(u => u.idEmpleado === idNorm && u.pin === pin.trim());

      if (!usuario) { Alert.alert('Error', 'ID de empleado o PIN incorrectos'); return; }

      await AsyncStorage.setItem('currentUser', JSON.stringify(usuario));
      await AsyncStorage.setItem('userSession', JSON.stringify({ ...usuario, sesionActiva: true }));
      navigation.reset({ index: 0, routes: [{ name: 'MainTabs' }] });
    } catch {
      Alert.alert('Error', 'Ocurrió un problema. Intenta de nuevo.');
    }
  };

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

        {/* HEADER CON LOGO */}
        <View style={styles.header}>
          <Image source={LOGO} style={styles.logo} resizeMode="contain" />
          <Text style={styles.appName}>Universal Inventory</Text>
          <Text style={styles.appSub}>Operaciones de Almacén</Text>
        </View>

        {/* FORM */}
        <View style={styles.form}>
          <Text style={styles.label}>ID de Empleado</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="person-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ingresa tu ID de empleado"
              placeholderTextColor="#94a3b8"
              value={idEmpleado}
              onChangeText={setIdEmpleado}
              autoCapitalize="characters"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>PIN</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="lock-closed-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ingresa tu PIN de 4 dígitos"
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

          <TouchableOpacity style={styles.btn} onPress={handleLogin} activeOpacity={0.85}>
            <Text style={styles.btnText}>Iniciar Sesión  →</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => navigation.navigate('RecuperarPassword')} style={styles.linkRow}>
            <Text style={styles.link}>¿Olvidaste tu contraseña?</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => navigation.navigate('CrearCuenta')} style={styles.linkRow}>
            <Text style={styles.link}>¿No tienes una cuenta? <Text style={styles.linkBold}>Regístrate aquí</Text></Text>
          </TouchableOpacity>
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
    paddingTop: 60,
    paddingBottom: 32,
    alignItems: 'center',
    paddingHorizontal: 24,
  },
  logo: { width: 130, height: 130, marginBottom: 12 },
  appName: { fontSize: 22, fontWeight: '800', color: '#ffffff', letterSpacing: 0.3 },
  appSub: { fontSize: 13, color: 'rgba(255,255,255,0.6)', marginTop: 4 },

  form: {
    flex: 1,
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    paddingHorizontal: 24,
    paddingTop: 32,
    paddingBottom: 40,
  },

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

  linkRow: { marginBottom: 10, alignItems: 'center' },
  link: { fontSize: 13, color: '#64748b' },
  linkBold: { color: '#1e3a8a', fontWeight: '700' },
});

export default LoginScreen;