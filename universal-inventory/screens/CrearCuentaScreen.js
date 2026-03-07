// screens/CrearCuentaScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ScrollView, Alert, KeyboardAvoidingView, Platform, Image
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LOGO = require('../assets/logo.jpeg');

// ─── CRÍTICO: Field FUERA del componente para evitar que el teclado se cierre ───
const Field = ({ icon, placeholder, value, onChange, secure, keyboard, maxLen, autoCapitalize }) => (
  <View style={styles.inputWrapper}>
    <View style={styles.iconBox}>
      <Ionicons name={icon} size={18} color="#3b82f6" />
    </View>
    <TextInput
      style={styles.input}
      placeholder={placeholder}
      placeholderTextColor="#94a3b8"
      value={value}
      onChangeText={onChange}
      secureTextEntry={secure || false}
      keyboardType={keyboard || 'default'}
      maxLength={maxLen}
      autoCapitalize={autoCapitalize || 'sentences'}
      autoCorrect={false}
    />
  </View>
);

const CrearCuentaScreen = ({ navigation }) => {
  const [nombre, setNombre] = useState('');
  const [email, setEmail] = useState('');
  const [idEmpleado, setIdEmpleado] = useState('');
  const [contrasena, setContrasena] = useState('');
  const [confirmar, setConfirmar] = useState('');
  const [mostrar, setMostrar] = useState(false);

  const handleRegistro = async () => {
    if (!nombre.trim())   { Alert.alert('Error', 'Ingresa tu nombre completo'); return; }
    if (!email.trim())    { Alert.alert('Error', 'Ingresa tu correo electrónico'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) { Alert.alert('Error', 'Correo inválido'); return; }
    if (!idEmpleado.trim()) { Alert.alert('Error', 'Ingresa tu ID de empleado'); return; }
    if (contrasena.length !== 4 || !/^\d+$/.test(contrasena)) { Alert.alert('Error', 'El PIN debe tener exactamente 4 dígitos numéricos'); return; }
    if (contrasena !== confirmar) { Alert.alert('Error', 'Los PINs no coinciden'); return; }

    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch { usuarios = []; }

      const idNorm = idEmpleado.trim().toUpperCase();
      if (usuarios.find(u => u.idEmpleado === idNorm)) { Alert.alert('Error', 'Ya existe un usuario con ese ID'); return; }
      if (usuarios.find(u => u.email === email.trim().toLowerCase())) { Alert.alert('Error', 'Ya existe una cuenta con ese correo'); return; }

      usuarios.push({
        nombre: nombre.trim(), email: email.trim().toLowerCase(),
        idEmpleado: idNorm, pin: contrasena.trim(),
        fechaRegistro: new Date().toISOString(),
      });
      await AsyncStorage.setItem('usuarios', JSON.stringify(usuarios));
      Alert.alert('¡Cuenta creada!', 'Ya puedes iniciar sesión.', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    } catch {
      Alert.alert('Error', 'Ocurrió un problema. Intenta de nuevo.');
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 20}
    >
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

        {/* HEADER CON LOGO */}
        <View style={styles.header}>
          <Image source={LOGO} style={styles.logo} resizeMode="contain" />
          <Text style={styles.headerTitle}>Crear Cuenta</Text>
          <Text style={styles.headerSub}>Regístrate para acceder a Universal Inventory</Text>
        </View>

        {/* FORMULARIO */}
        <View style={styles.form}>
          <Text style={styles.label}>Nombre Completo *</Text>
          <Field icon="person-outline" placeholder="Juan Pérez" value={nombre} onChange={setNombre} autoCapitalize="words" />

          <Text style={styles.label}>Correo Electrónico *</Text>
          <Field icon="mail-outline" placeholder="juan.perez@empresa.com" value={email} onChange={setEmail} keyboard="email-address" autoCapitalize="none" />

          <Text style={styles.label}>ID de Empleado *</Text>
          <Field icon="id-card-outline" placeholder="EMP-001" value={idEmpleado} onChange={setIdEmpleado} autoCapitalize="characters" />

          <Text style={styles.label}>PIN * (4 dígitos numéricos)</Text>
          <View style={styles.inputWrapper}>
            <View style={styles.iconBox}>
              <Ionicons name="lock-closed-outline" size={18} color="#3b82f6" />
            </View>
            <TextInput
              style={styles.input}
              placeholder="4 dígitos numéricos"
              placeholderTextColor="#94a3b8"
              value={contrasena}
              onChangeText={setContrasena}
              secureTextEntry={!mostrar}
              keyboardType="numeric"
              maxLength={4}
              autoCapitalize="none"
              autoCorrect={false}
            />
            <TouchableOpacity onPress={() => setMostrar(!mostrar)} style={styles.eyeBtn}>
              <Ionicons name={mostrar ? 'eye-off-outline' : 'eye-outline'} size={18} color="#64748b" />
            </TouchableOpacity>
          </View>

          <Text style={styles.label}>Confirmar PIN *</Text>
          <View style={styles.inputWrapper}>
            <View style={styles.iconBox}>
              <Ionicons name="lock-closed-outline" size={18} color="#3b82f6" />
            </View>
            <TextInput
              style={styles.input}
              placeholder="Repite tu PIN"
              placeholderTextColor="#94a3b8"
              value={confirmar}
              onChangeText={setConfirmar}
              secureTextEntry={!mostrar}
              keyboardType="numeric"
              maxLength={4}
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          <TouchableOpacity style={styles.btn} onPress={handleRegistro} activeOpacity={0.85}>
            <Text style={styles.btnText}>Crear Cuenta</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={styles.link}>¿Ya tienes una cuenta? <Text style={styles.linkBold}>Inicia Sesión</Text></Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#1e2d4a' },
  scroll: { flexGrow: 1, paddingBottom: 30 },

  header: {
    backgroundColor: '#1e2d4a',
    paddingTop: 50,
    paddingBottom: 28,
    alignItems: 'center',
    paddingHorizontal: 24,
  },
  logo: { width: 110, height: 110, marginBottom: 12 },
  headerTitle: { fontSize: 24, fontWeight: '800', color: '#ffffff', marginBottom: 6 },
  headerSub: { fontSize: 13, color: 'rgba(255,255,255,0.65)', lineHeight: 18, textAlign: 'center' },

  form: {
    backgroundColor: '#ffffff', marginHorizontal: 16,
    borderRadius: 16, padding: 20,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08, shadowRadius: 12, elevation: 3,
  },

  label: { fontSize: 13, fontWeight: '600', color: '#1e2d4a', marginBottom: 6, marginTop: 12 },

  inputWrapper: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 10,
    borderWidth: 1.5, borderColor: '#e2e8f0', overflow: 'hidden', marginBottom: 2,
  },
  iconBox: {
    width: 42, height: 46, justifyContent: 'center', alignItems: 'center',
    backgroundColor: '#eff6ff', borderRightWidth: 1, borderRightColor: '#e2e8f0',
  },
  input: {
    flex: 1, fontSize: 15, color: '#1e293b',
    paddingHorizontal: 12, paddingVertical: Platform.OS === 'ios' ? 13 : 9,
  },
  eyeBtn: { paddingHorizontal: 12 },

  btn: {
    backgroundColor: '#1e3a8a', borderRadius: 12, paddingVertical: 15,
    alignItems: 'center', marginTop: 20, marginBottom: 16,
    shadowColor: '#1e3a8a', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35, shadowRadius: 10, elevation: 6,
  },
  btnText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },
  link: { textAlign: 'center', fontSize: 13, color: '#64748b' },
  linkBold: { color: '#1e3a8a', fontWeight: '700' },
});

export default CrearCuentaScreen;