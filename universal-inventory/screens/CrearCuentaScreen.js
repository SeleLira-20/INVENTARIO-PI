// screens/CrearCuentaScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ScrollView, Alert, KeyboardAvoidingView, Platform, Image, ActivityIndicator
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';

const LOGO    = require('../assets/logo.jpeg');
const API_BASE = 'https://inventario-pi-1.onrender.com';

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
  const [nombre,     setNombre]     = useState('');
  const [email,      setEmail]      = useState('');
  const [idEmpleado, setIdEmpleado] = useState('');
  const [pin,        setPin]        = useState('');
  const [confirmar,  setConfirmar]  = useState('');
  const [mostrar,    setMostrar]    = useState(false);
  const [cargando,   setCargando]   = useState(false);
  const [idGenerado,  setIdGenerado]  = useState('');
  const [generando,   setGenerando]   = useState(false);

  const generarIdEmpleado = async (nombreCompleto) => {
    if (!nombreCompleto.trim()) return;
    setGenerando(true);
    try {
      // Obtener iniciales (máx 3 letras)
      const palabras  = nombreCompleto.trim().split(' ').filter(Boolean);
      const iniciales = palabras.slice(0, 3).map(p => p[0].toUpperCase()).join('');

      // Consultar cuántos usuarios existen para el número consecutivo
      const resp = await fetch(`${API_URL}/v1/usuarios/`);
      const data = await resp.json();
      const total = (data.usuarios ?? []).length + 1;
      const num   = String(total).padStart(3, '0');
      const nuevoId = `${iniciales}-${num}`;
      setIdGenerado(nuevoId);
      setIdEmpleado(nuevoId);
    } catch {
      // Si falla la API, generar con timestamp
      const palabras  = nombreCompleto.trim().split(' ').filter(Boolean);
      const iniciales = palabras.slice(0, 3).map(p => p[0].toUpperCase()).join('');
      const num       = String(Date.now()).slice(-3);
      setIdGenerado(`${iniciales}-${num}`);
      setIdEmpleado(`${iniciales}-${num}`);
    } finally {
      setGenerando(false);
    }
  };

  const handleRegistro = async () => {
    // Validaciones
    if (!nombre.trim())     { Alert.alert('Error', 'Ingresa tu nombre completo'); return; }
    if (!email.trim())      { Alert.alert('Error', 'Ingresa tu correo electrónico'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) { Alert.alert('Error', 'Correo inválido'); return; }
    if (!idEmpleado.trim()) { Alert.alert('Error', 'Ingresa tu ID de empleado'); return; }
    if (!/^\d{4}$/.test(pin)) { Alert.alert('Error', 'El PIN debe tener exactamente 4 dígitos numéricos'); return; }
    if (pin !== confirmar)  { Alert.alert('Error', 'Los PINs no coinciden'); return; }

    setCargando(true);
    try {
      const credentials = btoa('admin:Admin123!');
      const resp = await fetch(`${API_URL}/v1/usuarios/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Basic ${credentials}`,
        },
        body: JSON.stringify({
          nombre:      nombre.trim(),
          email:       email.trim().toLowerCase(),
          id_empleado: idEmpleado.trim().toUpperCase(),
          pin:         pin.trim(),
          rol:         'Operador',
          permisos:    '', // Sin permisos hasta que el admin los asigne
        }),
      });

      const data = await resp.json();

      if (data.status === '400') {
        Alert.alert('Error', data.mensaje ?? 'Ya existe un usuario con ese correo o ID');
        return;
      }
      if (!resp.ok) {
        throw new Error(data.detail ?? 'Error al crear la cuenta');
      }

      Alert.alert(
        '✅ Cuenta creada',
        `Tu cuenta fue registrada correctamente.\n\nID: ${idEmpleado.trim().toUpperCase()}\nPIN: ${pin}\n\nEl administrador te asignará tus permisos pronto. Podrás iniciar sesión cuando estén listos.`,
        [{ text: 'Entendido', onPress: () => navigation.goBack() }]
      );

    } catch (err) {
      Alert.alert(
        'Error de conexión',
        'No se pudo conectar con el servidor.\n\nVerifica que estés en la misma red WiFi.',
        [{ text: 'Reintentar', onPress: handleRegistro }, { text: 'Cancelar', style: 'cancel' }]
      );
    } finally {
      setCargando(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 20}
    >
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

        <View style={styles.header}>
          <Image source={LOGO} style={styles.logo} resizeMode="contain" />
          <Text style={styles.headerTitle}>Crear Cuenta</Text>
          <Text style={styles.headerSub}>Regístrate para acceder a Universal Inventory</Text>
        </View>

        <View style={styles.form}>

          <View style={styles.infoBanner}>
            <Ionicons name="information-circle-outline" size={16} color="#f59e0b" />
            <Text style={styles.infoBannerText}>
              Después de registrarte, el administrador te asignará tus permisos y podrás iniciar sesión.
            </Text>
          </View>

          <Text style={styles.label}>Nombre Completo *</Text>
          <View style={styles.inputWrapper}>
            <View style={styles.iconBox}>
              <Ionicons name="person-outline" size={18} color="#3b82f6" />
            </View>
            <TextInput
              style={styles.input}
              placeholder="Juan Pérez"
              placeholderTextColor="#94a3b8"
              value={nombre}
              onChangeText={setNombre}
              onBlur={() => generarIdEmpleado(nombre)}
              autoCapitalize="words"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>Correo Electrónico *</Text>
          <Field icon="mail-outline" placeholder="juan.perez@empresa.com" value={email} onChange={setEmail} keyboard="email-address" autoCapitalize="none" />

          <Text style={styles.label}>ID de Empleado (generado automáticamente)</Text>
          <View style={styles.inputWrapper}>
            <View style={styles.iconBox}>
              <Ionicons name="id-card-outline" size={18} color="#3b82f6" />
            </View>
            <TextInput
              style={[styles.input, { color: '#2563eb', fontWeight: '700' }]}
              value={idEmpleado}
              editable={false}
              placeholder="Se genera al ingresar tu nombre"
              placeholderTextColor="#94a3b8"
            />
            {generando
              ? <View style={styles.eyeBtn}><Ionicons name="sync-outline" size={18} color="#94a3b8" /></View>
              : nombre.trim() !== '' && (
                <TouchableOpacity onPress={() => generarIdEmpleado(nombre)} style={styles.eyeBtn}>
                  <Ionicons name="refresh-outline" size={18} color="#2563eb" />
                </TouchableOpacity>
              )
            }
          </View>
          {idEmpleado ? (
            <Text style={{ fontSize: 11, color: '#16a34a', marginTop: 4, marginBottom: 4 }}>
              ✓ Tu ID será: <Text style={{ fontWeight: '700' }}>{idEmpleado}</Text>
            </Text>
          ) : null}

          <Text style={styles.label}>PIN * (4 dígitos numéricos)</Text>
          <View style={styles.inputWrapper}>
            <View style={styles.iconBox}>
              <Ionicons name="lock-closed-outline" size={18} color="#3b82f6" />
            </View>
            <TextInput
              style={styles.input}
              placeholder="4 dígitos numéricos"
              placeholderTextColor="#94a3b8"
              value={pin}
              onChangeText={setPin}
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

          <TouchableOpacity
            style={[styles.btn, cargando && { opacity: 0.7 }]}
            onPress={handleRegistro}
            disabled={cargando}
            activeOpacity={0.85}
          >
            {cargando
              ? <ActivityIndicator color="white" />
              : <Text style={styles.btnText}>Crear Cuenta</Text>
            }
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
  root:   { flex: 1, backgroundColor: '#1e2d4a' },
  scroll: { flexGrow: 1, paddingBottom: 30 },

  header: { backgroundColor: '#1e2d4a', paddingTop: 50, paddingBottom: 28, alignItems: 'center', paddingHorizontal: 24 },
  logo:        { width: 110, height: 110, marginBottom: 12 },
  headerTitle: { fontSize: 24, fontWeight: '800', color: '#ffffff', marginBottom: 6 },
  headerSub:   { fontSize: 13, color: 'rgba(255,255,255,0.65)', lineHeight: 18, textAlign: 'center' },

  form: {
    backgroundColor: '#ffffff', marginHorizontal: 16,
    borderRadius: 16, padding: 20,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08, shadowRadius: 12, elevation: 3,
  },

  infoBanner: {
    flexDirection: 'row', alignItems: 'flex-start', gap: 8,
    backgroundColor: '#fffbeb', borderRadius: 10, padding: 12,
    marginBottom: 8, borderWidth: 1, borderColor: '#fde68a',
  },
  infoBannerText: { flex: 1, fontSize: 12, color: '#92400e', lineHeight: 17 },

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
  btnText:  { color: '#ffffff', fontSize: 16, fontWeight: '700' },
  link:     { textAlign: 'center', fontSize: 13, color: '#64748b' },
  linkBold: { color: '#1e3a8a', fontWeight: '700' },
});

export default CrearCuentaScreen;