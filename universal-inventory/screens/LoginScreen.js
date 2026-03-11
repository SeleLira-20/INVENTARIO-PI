// screens/LoginScreen.js
// ─────────────────────────────────────────────────────────────────────────────
// Contiene 4 vistas internas (sin navegación externa):
//   'login'     → inicio de sesión
//   'recuperar' → recuperar contraseña  (idéntico al RecuperarPasswordScreen original)
//   'crear'     → crear cuenta nueva
//   'exito'     → pantalla de confirmación
//
// RecuperarPasswordScreen.js ya NO es necesario.
// Quita la ruta 'RecuperarPassword' de tu navigator.
// ─────────────────────────────────────────────────────────────────────────────

import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Alert, KeyboardAvoidingView, Platform, ScrollView, Image,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LOGO = require('../assets/logo.jpeg');

const LoginScreen = ({ navigation }) => {

  // ── estado: login ──────────────────────────────────────────────────────────
  const [idEmpleado, setIdEmpleado] = useState('');
  const [pin, setPin]               = useState('');
  const [mostrarPin, setMostrarPin] = useState(false);

  // ── estado: recuperar ──────────────────────────────────────────────────────
  const [emailRecuperar, setEmailRecuperar] = useState('');

  // ── estado: crear cuenta ───────────────────────────────────────────────────
  const [nombre, setNombre]                           = useState('');
  const [nuevoId, setNuevoId]                         = useState('');
  const [emailCrear, setEmailCrear]                   = useState('');
  const [nuevoPin, setNuevoPin]                       = useState('');
  const [confirmarPin, setConfirmarPin]               = useState('');
  const [mostrarNuevoPin, setMostrarNuevoPin]         = useState(false);
  const [mostrarConfirmarPin, setMostrarConfirmarPin] = useState(false);

  // ── control de vista ───────────────────────────────────────────────────────
  const [vista, setVista] = useState('login'); // 'login' | 'recuperar' | 'crear' | 'exito'

  const irA = (v) => setVista(v);

  const irALogin = () => {
    setIdEmpleado(''); setPin('');
    setEmailRecuperar('');
    setNombre(''); setNuevoId(''); setEmailCrear('');
    setNuevoPin(''); setConfirmarPin('');
    setVista('login');
  };

  // ── LÓGICA: iniciar sesión ─────────────────────────────────────────────────
  const handleLogin = async () => {
    if (!idEmpleado.trim()) { Alert.alert('Error', 'Ingresa tu ID de empleado'); return; }
    if (!pin.trim())        { Alert.alert('Error', 'Ingresa tu PIN');             return; }
    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch { usuarios = []; }
      const usuario = usuarios.find(
        u => u.idEmpleado === idEmpleado.trim().toUpperCase() && u.pin === pin.trim()
      );
      if (!usuario) { Alert.alert('Error', 'ID de empleado o PIN incorrectos'); return; }
      await AsyncStorage.setItem('currentUser', JSON.stringify(usuario));
      await AsyncStorage.setItem('userSession', JSON.stringify({ ...usuario, sesionActiva: true }));
      navigation.reset({ index: 0, routes: [{ name: 'MainTabs' }] });
    } catch {
      Alert.alert('Error', 'Ocurrió un problema. Intenta de nuevo.');
    }
  };

  // ── LÓGICA: recuperar contraseña ───────────────────────────────────────────
  const handleRecuperar = async () => {
    if (!emailRecuperar.trim()) { Alert.alert('Error', 'Ingresa tu correo electrónico'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailRecuperar.trim())) {
      Alert.alert('Error', 'Ingresa un correo válido'); return;
    }
    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch {}
      const usuario = usuarios.find(u => u.email === emailRecuperar.trim().toLowerCase());
      if (usuario) { console.log('[DEV] PIN del usuario:', usuario.pin); }
    } catch {}
    // Siempre muestra éxito (no revela si el correo existe)
    irA('exito');
  };

  // ── LÓGICA: crear cuenta ───────────────────────────────────────────────────
  const handleCrearCuenta = async () => {
    if (!nombre.trim())     { Alert.alert('Error', 'Ingresa tu nombre completo');  return; }
    if (!nuevoId.trim())    { Alert.alert('Error', 'Ingresa tu ID de empleado');   return; }
    if (!emailCrear.trim()) { Alert.alert('Error', 'Ingresa tu correo');           return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailCrear.trim())) {
      Alert.alert('Error', 'Ingresa un correo válido'); return;
    }
    if (nuevoPin.length < 4)       { Alert.alert('Error', 'El PIN debe tener 4 dígitos'); return; }
    if (nuevoPin !== confirmarPin) { Alert.alert('Error', 'Los PINs no coinciden');        return; }
    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch { usuarios = []; }
      const idNorm = nuevoId.trim().toUpperCase();
      if (usuarios.find(u => u.idEmpleado === idNorm)) {
        Alert.alert('Error', 'Ese ID ya está registrado. Usa "Recuperar contraseña" si olvidaste tu PIN.');
        return;
      }
      usuarios.push({
        idEmpleado: idNorm,
        nombre:     nombre.trim(),
        email:      emailCrear.trim().toLowerCase(),
        pin:        nuevoPin,
      });
      await AsyncStorage.setItem('usuarios', JSON.stringify(usuarios));
      irA('exito');
    } catch {
      Alert.alert('Error', 'Ocurrió un problema. Intenta de nuevo.');
    }
  };

  // ── COMPONENTES COMPARTIDOS ────────────────────────────────────────────────

  // Header con logo centrado (login y éxito)
  const HeaderConLogo = ({ titulo, subtitulo }) => (
    <View style={styles.headerLogo}>
      <Image source={LOGO} style={styles.logo} resizeMode="contain" />
      <Text style={styles.appName}>{titulo}</Text>
      <Text style={styles.appSub}>{subtitulo}</Text>
    </View>
  );

  // Header con logo + botón back + texto centrado (recuperar y crear)
  // → reproduce exactamente el header del RecuperarPasswordScreen original
  const HeaderConBack = ({ titulo, subtitulo }) => (
    <View style={styles.headerBack}>
      <TouchableOpacity style={styles.backBtn} onPress={irALogin}>
        <Ionicons name="arrow-back" size={20} color="#ffffff" />
      </TouchableOpacity>
      <Image source={LOGO} style={styles.logoSmall} resizeMode="contain" />
      <Text style={styles.headerBackTitle}>{titulo}</Text>
      <Text style={styles.headerBackSub}>{subtitulo}</Text>
    </View>
  );

  // Bloque "¿Necesitas ayuda?" — aparece en recuperar y éxito
  const AyudaBox = () => (
    <View style={styles.helpBox}>
      <Text style={styles.helpTitle}>¿Necesitas ayuda?</Text>
      <Text style={styles.helpText}>Contacta a tu supervisor o al departamento de TI</Text>
    </View>
  );

  // ══════════════════════════════════════════════════════════════════════════
  // VISTA: LOGIN
  // ══════════════════════════════════════════════════════════════════════════
  if (vista === 'login') return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
    >
      <ScrollView
        contentContainerStyle={styles.scroll}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
      >
        <HeaderConLogo titulo="Universal Inventory" subtitulo="Operaciones de Almacén" />

        <View style={styles.form}>

          {/* ID de Empleado */}
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

          {/* PIN */}
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

          {/* Botón principal */}
          <TouchableOpacity style={styles.btn} onPress={handleLogin} activeOpacity={0.85}>
            <Text style={styles.btnText}>Iniciar Sesión  →</Text>
          </TouchableOpacity>

          {/* ¿Olvidaste tu contraseña? — abre vista 'recuperar' */}
          <TouchableOpacity onPress={() => irA('recuperar')} style={styles.linkRow}>
            <Text style={styles.link}>¿Olvidaste tu contraseña?</Text>
          </TouchableOpacity>

          {/* Separador */}
          <View style={styles.separator}>
            <View style={styles.separatorLine} />
            <Text style={styles.separatorText}>o</Text>
            <View style={styles.separatorLine} />
          </View>

          {/* Crear cuenta */}
          <TouchableOpacity onPress={() => irA('crear')} style={styles.linkRow}>
            <Text style={styles.link}>
              ¿No tienes una cuenta? <Text style={styles.linkBold}>Regístrate aquí</Text>
            </Text>
          </TouchableOpacity>

        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );

  // ══════════════════════════════════════════════════════════════════════════
  // VISTA: RECUPERAR CONTRASEÑA
  // Idéntica al RecuperarPasswordScreen.js original
  // ══════════════════════════════════════════════════════════════════════════
  if (vista === 'recuperar') return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
    >
      <ScrollView
        contentContainerStyle={styles.scroll}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
      >
        <HeaderConBack
          titulo="Recuperar Contraseña"
          subtitulo="Ingresa tu correo y te enviaremos instrucciones"
        />

        <View style={styles.body}>
          {/* Formulario de correo */}
          <Text style={styles.label}>Correo Electrónico *</Text>
          <View style={styles.inputWrapperMb}>
            <Ionicons name="mail-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="tu.correo@empresa.com"
              placeholderTextColor="#94a3b8"
              value={emailRecuperar}
              onChangeText={setEmailRecuperar}
              keyboardType="email-address"
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          {/* Banner amarillo */}
          <View style={styles.infoBox}>
            <Ionicons name="information-circle-outline" size={16} color="#f59e0b" />
            <Text style={styles.infoText}>
              Asegúrate de usar el correo electrónico registrado en tu cuenta de Universal Inventory
            </Text>
          </View>

          {/* Botón enviar */}
          <TouchableOpacity style={styles.btnRecuperar} onPress={handleRecuperar} activeOpacity={0.85}>
            <Text style={styles.btnText}>Enviar Instrucciones</Text>
          </TouchableOpacity>

          <AyudaBox />
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );

  // ══════════════════════════════════════════════════════════════════════════
  // VISTA: CREAR CUENTA
  // ══════════════════════════════════════════════════════════════════════════
  if (vista === 'crear') return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
    >
      <ScrollView
        contentContainerStyle={styles.scroll}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
      >
        <HeaderConBack
          titulo="Crear Cuenta"
          subtitulo="Completa los campos para registrarte"
        />

        <View style={styles.body}>

          <Text style={styles.label}>Nombre Completo *</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="person-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Tu nombre completo"
              placeholderTextColor="#94a3b8"
              value={nombre}
              onChangeText={setNombre}
              autoCapitalize="words"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>ID de Empleado *</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="card-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ej: EMP001"
              placeholderTextColor="#94a3b8"
              value={nuevoId}
              onChangeText={setNuevoId}
              autoCapitalize="characters"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>Correo Electrónico *</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="mail-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="tu.correo@empresa.com"
              placeholderTextColor="#94a3b8"
              value={emailCrear}
              onChangeText={setEmailCrear}
              keyboardType="email-address"
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          <Text style={styles.label}>PIN (4 dígitos) *</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="lock-closed-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="····"
              placeholderTextColor="#94a3b8"
              value={nuevoPin}
              onChangeText={setNuevoPin}
              secureTextEntry={!mostrarNuevoPin}
              keyboardType="numeric"
              maxLength={4}
              autoCorrect={false}
            />
            <TouchableOpacity onPress={() => setMostrarNuevoPin(!mostrarNuevoPin)} style={styles.eyeBtn}>
              <Ionicons name={mostrarNuevoPin ? 'eye-off-outline' : 'eye-outline'} size={18} color="#64748b" />
            </TouchableOpacity>
          </View>

          <Text style={styles.label}>Confirmar PIN *</Text>
          <View style={styles.inputWrapper}>
            <Ionicons name="lock-closed-outline" size={18} color="#64748b" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="····"
              placeholderTextColor="#94a3b8"
              value={confirmarPin}
              onChangeText={setConfirmarPin}
              secureTextEntry={!mostrarConfirmarPin}
              keyboardType="numeric"
              maxLength={4}
              autoCorrect={false}
            />
            <TouchableOpacity onPress={() => setMostrarConfirmarPin(!mostrarConfirmarPin)} style={styles.eyeBtn}>
              <Ionicons name={mostrarConfirmarPin ? 'eye-off-outline' : 'eye-outline'} size={18} color="#64748b" />
            </TouchableOpacity>
          </View>

          {/* Indicador de coincidencia de PINs */}
          {confirmarPin.length > 0 && (
            <View style={styles.pinMatchRow}>
              <Ionicons
                name={nuevoPin === confirmarPin ? 'checkmark-circle' : 'close-circle'}
                size={16}
                color={nuevoPin === confirmarPin ? '#22c55e' : '#ef4444'}
              />
              <Text style={[styles.pinMatchText, { color: nuevoPin === confirmarPin ? '#22c55e' : '#ef4444' }]}>
                {nuevoPin === confirmarPin ? 'Los PINs coinciden' : 'Los PINs no coinciden'}
              </Text>
            </View>
          )}

          <TouchableOpacity style={[styles.btn, { marginTop: 24 }]} onPress={handleCrearCuenta} activeOpacity={0.85}>
            <Text style={styles.btnText}>Crear Cuenta  →</Text>
          </TouchableOpacity>

          <TouchableOpacity onPress={() => irA('recuperar')} style={styles.linkRow}>
            <Text style={styles.link}>¿Ya tienes cuenta? <Text style={styles.linkBold}>Recuperar contraseña</Text></Text>
          </TouchableOpacity>

        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );

  // ══════════════════════════════════════════════════════════════════════════
  // VISTA: ÉXITO / CONFIRMACIÓN
  // ══════════════════════════════════════════════════════════════════════════
  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
    >
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>

        <HeaderConLogo titulo="Universal Inventory" subtitulo="Operaciones de Almacén" />

        <View style={styles.body}>
          <View style={styles.successBox}>
            <Ionicons name="checkmark-circle" size={64} color="#22c55e" style={{ marginBottom: 16 }} />
            <Text style={styles.successTitle}>¡Correo enviado!</Text>
            <Text style={styles.successText}>
              Si el correo está registrado, recibirás instrucciones para restablecer tu contraseña.
            </Text>
            <TouchableOpacity style={styles.btn} onPress={irALogin} activeOpacity={0.85}>
              <Text style={styles.btnText}>Volver al inicio de sesión</Text>
            </TouchableOpacity>
          </View>

          <AyudaBox />
        </View>

      </ScrollView>
    </KeyboardAvoidingView>
  );
};

// ─── ESTILOS ──────────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  root:   { flex: 1, backgroundColor: '#1e2d4a' },
  scroll: { flexGrow: 1 },

  // ── Header con logo centrado (login / éxito)
  headerLogo: {
    backgroundColor: '#1e2d4a',
    paddingTop: 60, paddingBottom: 32,
    alignItems: 'center', paddingHorizontal: 24,
  },
  logo:    { width: 130, height: 130, marginBottom: 12 },
  appName: { fontSize: 22, fontWeight: '800', color: '#ffffff', letterSpacing: 0.3 },
  appSub:  { fontSize: 13, color: 'rgba(255,255,255,0.6)', marginTop: 4, textAlign: 'center' },

  // ── Header con back + logo pequeño + texto centrado (recuperar / crear)
  // Reproduce exactamente el RecuperarPasswordScreen original
  headerBack: {
    backgroundColor: '#1e2d4a',
    paddingTop: 50, paddingBottom: 28,
    paddingHorizontal: 24, alignItems: 'center',
  },
  backBtn: {
    alignSelf: 'flex-start',
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center', alignItems: 'center', marginBottom: 16,
  },
  logoSmall:       { width: 100, height: 100, marginBottom: 12 },
  headerBackTitle: { fontSize: 22, fontWeight: '800', color: '#ffffff', marginBottom: 6 },
  headerBackSub:   { fontSize: 13, color: 'rgba(255,255,255,0.65)', textAlign: 'center', lineHeight: 18 },

  // ── Cuerpo blanco con esquinas redondeadas
  form: {
    flex: 1, backgroundColor: '#ffffff',
    borderTopLeftRadius: 28, borderTopRightRadius: 28,
    paddingHorizontal: 24, paddingTop: 32, paddingBottom: 40,
  },
  // 'body' es idéntico a 'form' — se usa en recuperar/crear/exito para
  // respetar el padding original del RecuperarPasswordScreen (padding: 24)
  body: {
    flex: 1, backgroundColor: '#ffffff',
    borderTopLeftRadius: 28, borderTopRightRadius: 28,
    padding: 24,
  },

  // ── Inputs
  label: { fontSize: 13, fontWeight: '600', color: '#1e2d4a', marginBottom: 8, marginTop: 8 },
  inputWrapper: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 10,
    borderWidth: 1.5, borderColor: '#e2e8f0', paddingHorizontal: 12,
  },
  // Con margen inferior (usado en recuperar — igual al original)
  inputWrapperMb: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 10,
    borderWidth: 1.5, borderColor: '#e2e8f0', paddingHorizontal: 12,
    marginBottom: 16,
  },
  inputIcon: { marginRight: 8 },
  input: {
    flex: 1, fontSize: 15, color: '#1e293b',
    paddingVertical: Platform.OS === 'ios' ? 14 : 10,
  },
  eyeBtn: { paddingLeft: 8 },

  // ── Botón principal (azul)
  btn: {
    backgroundColor: '#1e3a8a', borderRadius: 12, paddingVertical: 15,
    alignItems: 'center', marginTop: 20, marginBottom: 20,
    shadowColor: '#1e3a8a', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 5,
  },
  // Botón recuperar — igual al original (mismo color, sin margen top excesivo)
  btnRecuperar: {
    backgroundColor: '#1e3a8a', borderRadius: 12, paddingVertical: 15,
    alignItems: 'center', marginBottom: 20,
    shadowColor: '#1e3a8a', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 5,
  },
  btnText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },

  // ── Separador (login)
  separator:     { flexDirection: 'row', alignItems: 'center', marginVertical: 4 },
  separatorLine: { flex: 1, height: 1, backgroundColor: '#e2e8f0' },
  separatorText: { marginHorizontal: 12, fontSize: 13, color: '#94a3b8' },

  // ── Links
  linkRow:  { marginBottom: 10, alignItems: 'center', marginTop: 4 },
  link:     { fontSize: 13, color: '#64748b' },
  linkBold: { color: '#1e3a8a', fontWeight: '700' },

  // ── Banner amarillo (recuperar — igual al original)
  infoBox: {
    flexDirection: 'row', alignItems: 'flex-start',
    backgroundColor: '#fffbeb', borderRadius: 10,
    padding: 12, marginBottom: 20, gap: 8,
    borderWidth: 1, borderColor: '#fde68a',
  },
  infoText: { flex: 1, fontSize: 12, color: '#92400e', lineHeight: 17 },

  // ── Coincidencia de PIN (crear)
  pinMatchRow:  { flexDirection: 'row', alignItems: 'center', marginTop: 6, gap: 6 },
  pinMatchText: { fontSize: 12, fontWeight: '600' },

  // ── Éxito (igual al original)
  successBox:   { alignItems: 'center', paddingVertical: 20 },
  successTitle: { fontSize: 22, fontWeight: '800', color: '#1e2d4a', marginBottom: 10 },
  successText:  { fontSize: 14, color: '#64748b', textAlign: 'center', lineHeight: 20, marginBottom: 24 },

  // ── Ayuda (igual al original)
  helpBox: {
    backgroundColor: '#f8fafc', borderRadius: 12, padding: 16,
    alignItems: 'center', borderWidth: 1, borderColor: '#e2e8f0',
  },
  helpTitle: { fontSize: 14, fontWeight: '700', color: '#1e2d4a', marginBottom: 4 },
  helpText:  { fontSize: 12, color: '#64748b', textAlign: 'center' },
});

export default LoginScreen;