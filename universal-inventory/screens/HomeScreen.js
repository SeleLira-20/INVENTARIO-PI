// screens/HomeScreen.js
import React, { useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, SafeAreaView, Image
} from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const LOGO = require('../assets/logo.jpeg');

const HomeScreen = ({ navigation }) => {
  const insets = useSafeAreaInsets();
  const [userData, setUserData] = useState({ nombre: 'Usuario', idEmpleado: '' });
  const [noLeidasCount, setNoLeidasCount] = useState(0);

  useFocusEffect(useCallback(() => {
    loadUserData();
    loadNoLeidas();
  }, []));

  const loadUserData = async () => {
    try {
      const raw = await AsyncStorage.getItem('currentUser');
      if (raw) setUserData(JSON.parse(raw));
    } catch {}
  };

  const loadNoLeidas = async () => {
    try {
      const raw = await AsyncStorage.getItem('notificaciones');
      if (raw) {
        const lista = JSON.parse(raw);
        setNoLeidasCount(lista.filter(n => !n.leida).length);
      } else {
        // Si no hay nada guardado aún, usar el valor por defecto (2)
        setNoLeidasCount(2);
      }
    } catch {}
  };

  const resumen = [
    { numero: '12', label: 'Completadas', color: '#2563eb' },
    { numero: '5',  label: 'En Progreso', color: '#f59e0b' },
    { numero: '8',  label: 'Pendientes',  color: '#6b7280' },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false}>

        {/* HEADER */}
        <View style={[styles.header, { paddingTop: insets.top + 18 }]}>
          <View style={styles.headerTop}>
            <View style={styles.headerLeft}>
              {/* Logo pequeño en el header */}
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
              <TouchableOpacity style={styles.iconButton} onPress={() => navigation.navigate('Profile')}>
                <Ionicons name="person-outline" size={20} color="#fff" />
              </TouchableOpacity>
            </View>
          </View>

          {/* Tarjeta turno / ID */}
          <View style={styles.infoCard}>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Turno</Text>
              <Text style={styles.infoValue}>Turno Diurno</Text>
            </View>
            <View style={styles.infoDivider} />
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>ID de Empleado</Text>
              <Text style={styles.infoValue}>{userData.idEmpleado || 'EMP-0000'}</Text>
            </View>
          </View>
        </View>

        {/* CONTENIDO */}
        <View style={styles.content}>
          <Text style={styles.sectionTitle}>Acciones Rápidas</Text>

          <View style={styles.grid}>
            <TouchableOpacity style={[styles.card, styles.cardGreen]} onPress={() => navigation.navigate('Scan')} activeOpacity={0.85}>
              <View style={styles.cardIconCircle}><Ionicons name="scan-outline" size={32} color="#fff" /></View>
              <Text style={styles.cardText}>Escanear{'\n'}Código</Text>
            </TouchableOpacity>

            <TouchableOpacity style={[styles.card, styles.cardBlue]} onPress={() => navigation.navigate('Inventory')} activeOpacity={0.85}>
              <View style={styles.cardIconCircle}><Ionicons name="cube-outline" size={32} color="#fff" /></View>
              <Text style={styles.cardText}>Ver Inventario</Text>
            </TouchableOpacity>

            <TouchableOpacity style={[styles.card, styles.cardOrange]} onPress={() => navigation.navigate('Picking')} activeOpacity={0.85}>
              <View style={styles.cardIconCircle}><Ionicons name="clipboard-outline" size={32} color="#fff" /></View>
              <Text style={styles.cardText}>Tareas de{'\n'}Picking</Text>
            </TouchableOpacity>

            <TouchableOpacity style={[styles.card, styles.cardRed]} onPress={() => navigation.navigate('Reports')} activeOpacity={0.85}>
              <View style={styles.cardIconCircle}><Ionicons name="alert-circle-outline" size={32} color="#fff" /></View>
              <Text style={styles.cardText}>Reportar{'\n'}Problema</Text>
            </TouchableOpacity>
          </View>

          <Text style={styles.sectionTitle}>Resumen de Hoy</Text>
          <View style={styles.resumenCard}>
            {resumen.map((item, i) => (
              <React.Fragment key={item.label}>
                <View style={styles.resumenItem}>
                  <Text style={[styles.resumenNumero, { color: item.color }]}>{item.numero}</Text>
                  <Text style={styles.resumenLabel}>{item.label}</Text>
                </View>
                {i < resumen.length - 1 && <View style={styles.resumenDivider} />}
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
  infoItem: { flex: 1 },
  infoLabel: { fontSize: 11, color: 'rgba(255,255,255,0.6)', marginBottom: 2 },
  infoValue: { fontSize: 13, fontWeight: '700', color: '#ffffff' },
  infoDivider: { width: 1, height: 30, backgroundColor: 'rgba(255,255,255,0.2)', marginHorizontal: 16 },

  content: { padding: 20 },
  sectionTitle: { fontSize: 17, fontWeight: '700', color: '#1e293b', marginBottom: 14, marginTop: 4 },

  grid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12, marginBottom: 24 },
  card: {
    width: '47%', aspectRatio: 1.15, borderRadius: 16,
    justifyContent: 'center', alignItems: 'center', paddingVertical: 18,
    shadowColor: '#000', shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.15, shadowRadius: 8, elevation: 4,
  },
  cardGreen:  { backgroundColor: '#22c55e' },
  cardBlue:   { backgroundColor: '#1d4ed8' },
  cardOrange: { backgroundColor: '#f59e0b' },
  cardRed:    { backgroundColor: '#ef4444' },
  cardIconCircle: {
    width: 56, height: 56, borderRadius: 28,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center', alignItems: 'center', marginBottom: 10,
  },
  cardText: { color: '#ffffff', fontSize: 14, fontWeight: '700', textAlign: 'center', lineHeight: 19 },

  resumenCard: {
    flexDirection: 'row', backgroundColor: '#ffffff', borderRadius: 16, paddingVertical: 22,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07, shadowRadius: 8, elevation: 3,
  },
  resumenItem: { flex: 1, alignItems: 'center' },
  resumenNumero: { fontSize: 36, fontWeight: '800' },
  resumenLabel: { fontSize: 12, color: '#6b7280', marginTop: 4, fontWeight: '500' },
  resumenDivider: { width: 1, backgroundColor: '#e5e7eb', marginVertical: 6 },
});

export default HomeScreen;