// screens/RecuperarPasswordScreen.js
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Alert, KeyboardAvoidingView, Platform, ScrollView
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const RecuperarPasswordScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);

  const handleRecuperar = async () => {
    if (!email.trim()) {
      Alert.alert('Error', 'Ingresa tu correo electrónico');
      return;
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.trim())) {
      Alert.alert('Error', 'Ingresa un correo electrónico válido');
      return;
    }

    setLoading(true);

    try {
      // Verificar si el email existe en la base de datos local
      const usuariosGuardados = await AsyncStorage.getItem('usuarios');
      const usuarios = usuariosGuardados ? JSON.parse(usuariosGuardados) : [];
      const usuario = usuarios.find(u => u.email === email.trim().toLowerCase());

      setTimeout(() => {
        setLoading(false);
        if (usuario) {
          Alert.alert(
            'Instrucciones enviadas',
            `Se han enviado instrucciones de recuperación al correo ${email.trim()}.\n\nPIN recordatorio (solo para pruebas): ${usuario.pin}`,
            [{ text: 'OK', onPress: () => navigation.goBack() }]
          );
        } else {
          // Por seguridad, no revelar si el correo existe o no
          Alert.alert(
            'Correo enviado',
            'Si ese correo está registrado, recibirás instrucciones para restablecer tu PIN.',
            [{ text: 'OK', onPress: () => navigation.goBack() }]
          );
        }
      }, 1000);
    } catch (error) {
      setLoading(false);
      Alert.alert('Error', 'Ocurrió un problema. Intenta de nuevo.');
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content}>
        <Ionicons name="lock-open-outline" size={80} color="#2563eb" style={styles.icon} />
        <Text style={styles.title}>¿Olvidaste tu PIN?</Text>
        <Text style={styles.subtitle}>
          Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu PIN
        </Text>

        <View style={styles.inputContainer}>
          <Ionicons name="mail-outline" size={20} color="#6b7280" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Correo electrónico"
            placeholderTextColor="#9ca3af"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
            autoCorrect={false}
          />
        </View>

        <TouchableOpacity
          style={[styles.button, loading && { opacity: 0.7 }]}
          onPress={handleRecuperar}
          disabled={loading}
        >
          <Text style={styles.buttonText}>
            {loading ? 'Enviando...' : 'Enviar instrucciones'}
          </Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backLink}>← Volver al inicio de sesión</Text>
        </TouchableOpacity>

        <View style={styles.soporteContainer}>
          <Ionicons name="help-circle-outline" size={16} color="#9ca3af" />
          <Text style={styles.soporteText}>¿Necesitas ayuda? Contacta soporte técnico</Text>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  content: { flexGrow: 1, justifyContent: 'center', padding: 24 },
  icon: { alignSelf: 'center', marginBottom: 20 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#1f2937', textAlign: 'center', marginBottom: 10 },
  subtitle: { fontSize: 14, color: '#6b7280', textAlign: 'center', marginBottom: 30, lineHeight: 20 },
  inputContainer: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff',
    borderRadius: 12, paddingHorizontal: 16,
    paddingVertical: Platform.OS === 'ios' ? 16 : 8,
    marginBottom: 20, borderWidth: 1, borderColor: '#e5e7eb',
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  inputIcon: { marginRight: 12 },
  input: { flex: 1, fontSize: 16, color: '#1f2937', padding: 0 },
  button: {
    backgroundColor: '#2563eb', borderRadius: 12,
    paddingVertical: 16, alignItems: 'center', marginBottom: 20,
  },
  buttonText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
  backLink: { textAlign: 'center', color: '#2563eb', fontSize: 14, marginVertical: 15 },
  soporteContainer: {
    flexDirection: 'row', justifyContent: 'center',
    alignItems: 'center', marginTop: 10,
  },
  soporteText: { fontSize: 12, color: '#9ca3af', marginLeft: 6, textDecorationLine: 'underline' },
});

export default RecuperarPasswordScreen;