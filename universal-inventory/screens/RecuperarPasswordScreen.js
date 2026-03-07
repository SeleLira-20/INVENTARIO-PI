// screens/RecuperarPasswordScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Alert, KeyboardAvoidingView, Platform, ScrollView, Image
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LOGO = require('../assets/logo.jpeg');

const RecuperarPasswordScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [enviado, setEnviado] = useState(false);

  const handleEnviar = async () => {
    if (!email.trim()) { Alert.alert('Error', 'Ingresa tu correo electrónico'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) { Alert.alert('Error', 'Ingresa un correo válido'); return; }
    try {
      const raw = await AsyncStorage.getItem('usuarios');
      let usuarios = [];
      try { const p = JSON.parse(raw); usuarios = Array.isArray(p) ? p : []; } catch {}
      const usuario = usuarios.find(u => u.email === email.trim().toLowerCase());
      if (usuario) { console.log('[DEV] PIN del usuario:', usuario.pin); }
    } catch {}
    setEnviado(true);
  };

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

        {/* HEADER CON LOGO */}
        <View style={styles.header}>
          <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
            <Ionicons name="arrow-back" size={20} color="#ffffff" />
          </TouchableOpacity>
          <Image source={LOGO} style={styles.logo} resizeMode="contain" />
          <Text style={styles.headerTitle}>Recuperar Contraseña</Text>
          <Text style={styles.headerSub}>Ingresa tu correo y te enviaremos instrucciones</Text>
        </View>

        <View style={styles.body}>
          {!enviado ? (
            <>
              <Text style={styles.label}>Correo Electrónico *</Text>
              <View style={styles.inputWrapper}>
                <Ionicons name="mail-outline" size={18} color="#64748b" style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="tu.correo@empresa.com"
                  placeholderTextColor="#94a3b8"
                  value={email}
                  onChangeText={setEmail}
                  keyboardType="email-address"
                  autoCapitalize="none"
                  autoCorrect={false}
                />
              </View>

              <View style={styles.infoBox}>
                <Ionicons name="information-circle-outline" size={16} color="#f59e0b" />
                <Text style={styles.infoText}>Asegúrate de usar el correo electrónico registrado en tu cuenta de Universal Inventory</Text>
              </View>

              <TouchableOpacity style={styles.btn} onPress={handleEnviar} activeOpacity={0.85}>
                <Text style={styles.btnText}>Enviar Instrucciones</Text>
              </TouchableOpacity>
            </>
          ) : (
            <View style={styles.successBox}>
              <Ionicons name="checkmark-circle" size={64} color="#22c55e" style={{ marginBottom: 16 }} />
              <Text style={styles.successTitle}>¡Correo enviado!</Text>
              <Text style={styles.successText}>Si el correo está registrado, recibirás instrucciones para restablecer tu contraseña.</Text>
              <TouchableOpacity style={styles.btn} onPress={() => navigation.goBack()} activeOpacity={0.85}>
                <Text style={styles.btnText}>Volver al inicio de sesión</Text>
              </TouchableOpacity>
            </View>
          )}

          <View style={styles.helpBox}>
            <Text style={styles.helpTitle}>¿Necesitas ayuda?</Text>
            <Text style={styles.helpText}>Contacta a tu supervisor o al departamento de TI</Text>
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
    paddingTop: 50, paddingBottom: 28,
    paddingHorizontal: 24, alignItems: 'center',
  },
  backBtn: {
    alignSelf: 'flex-start',
    width: 36, height: 36, borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.15)',
    justifyContent: 'center', alignItems: 'center', marginBottom: 16,
  },
  logo: { width: 100, height: 100, marginBottom: 12 },
  headerTitle: { fontSize: 22, fontWeight: '800', color: '#ffffff', marginBottom: 6 },
  headerSub: { fontSize: 13, color: 'rgba(255,255,255,0.65)', textAlign: 'center', lineHeight: 18 },

  body: {
    flex: 1, backgroundColor: '#ffffff',
    borderTopLeftRadius: 28, borderTopRightRadius: 28,
    padding: 24,
  },

  label: { fontSize: 13, fontWeight: '600', color: '#1e2d4a', marginBottom: 8, marginTop: 8 },
  inputWrapper: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 10,
    borderWidth: 1.5, borderColor: '#e2e8f0',
    paddingHorizontal: 12, marginBottom: 16,
  },
  inputIcon: { marginRight: 8 },
  input: { flex: 1, fontSize: 15, color: '#1e293b', paddingVertical: Platform.OS === 'ios' ? 14 : 10 },

  infoBox: {
    flexDirection: 'row', alignItems: 'flex-start',
    backgroundColor: '#fffbeb', borderRadius: 10,
    padding: 12, marginBottom: 20, gap: 8,
    borderWidth: 1, borderColor: '#fde68a',
  },
  infoText: { flex: 1, fontSize: 12, color: '#92400e', lineHeight: 17 },

  btn: {
    backgroundColor: '#1e3a8a', borderRadius: 12, paddingVertical: 15,
    alignItems: 'center', marginBottom: 20,
    shadowColor: '#1e3a8a', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 5,
  },
  btnText: { color: '#ffffff', fontSize: 16, fontWeight: '700' },

  successBox: { alignItems: 'center', paddingVertical: 20 },
  successTitle: { fontSize: 22, fontWeight: '800', color: '#1e2d4a', marginBottom: 10 },
  successText: { fontSize: 14, color: '#64748b', textAlign: 'center', lineHeight: 20, marginBottom: 24 },

  helpBox: {
    backgroundColor: '#f8fafc', borderRadius: 12, padding: 16,
    alignItems: 'center', borderWidth: 1, borderColor: '#e2e8f0',
  },
  helpTitle: { fontSize: 14, fontWeight: '700', color: '#1e2d4a', marginBottom: 4 },
  helpText: { fontSize: 12, color: '#64748b', textAlign: 'center' },
});

export default RecuperarPasswordScreen;