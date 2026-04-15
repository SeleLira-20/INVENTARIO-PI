// App.js
import React, { useState, useEffect, useRef } from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons as Icon } from '@expo/vector-icons';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Importar pantallas
import LoginScreen             from './screens/LoginScreen';
import CrearCuentaScreen       from './screens/CrearCuentaScreen';
import RecuperarPasswordScreen from './screens/RecuperarPasswordScreen';
import HomeScreen              from './screens/HomeScreen';
import ScanScreen              from './screens/ScanScreen';
import InventoryScreen         from './screens/InventoryScreen';
import NotificacionesScreen    from './screens/NotificacionesScreen';
import ProfileScreen           from './screens/ProfileScreen';
import PickingScreen           from './screens/PickingScreen';
import ReportsScreen           from './screens/ReportsScreen';
import ProductDetailScreen     from './screens/ProductDetailScreen';

const Stack = createStackNavigator();
const Tab   = createBottomTabNavigator();

// ── Pantalla de sin permiso ────────────────────────────────────────────────
function SinPermisoScreen({ navigation, permiso }) {
  return (
    <View style={{ flex:1, justifyContent:'center', alignItems:'center', backgroundColor:'#f8fafc', padding:30 }}>
      <Icon name="lock-closed" size={64} color="#94a3b8" />
      <Text style={{ fontSize:20, fontWeight:'800', color:'#1e293b', marginTop:16, marginBottom:8 }}>
        Acceso restringido
      </Text>
      <Text style={{ fontSize:14, color:'#64748b', textAlign:'center', lineHeight:20 }}>
        No tienes permiso para acceder a{'\n'}<Text style={{ fontWeight:'700' }}>{permiso}</Text>.{'\n\n'}
        Contacta al administrador para solicitar acceso.
      </Text>
      <TouchableOpacity
        onPress={() => navigation.goBack()}
        style={{ marginTop:24, backgroundColor:'#2563eb', borderRadius:10, paddingVertical:12, paddingHorizontal:28 }}
      >
        <Text style={{ color:'white', fontWeight:'700', fontSize:15 }}>Volver</Text>
      </TouchableOpacity>
    </View>
  );
}

// ── Tabs dinámicas según permisos ─────────────────────────────────────────
function MainTabs({ route }) {
  // Los permisos se pasan como parámetro de navegación desde App
  const permisos = route?.params?.permisos ?? [];
  const tienePermiso = (p) => permisos.includes(p);

  return (
    <Tab.Navigator
      screenOptions={({ route: r }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          const icons = {
            Home:          focused ? 'home'          : 'home-outline',
            Scan:          focused ? 'scan'          : 'scan-outline',
            Inventory:     focused ? 'cube'          : 'cube-outline',
            Notifications: focused ? 'notifications' : 'notifications-outline',
            Profile:       focused ? 'person'        : 'person-outline',
          };
          return <Icon name={icons[r.name] || 'home-outline'} size={size} color={color} />;
        },
        tabBarActiveTintColor:   '#2563eb',
        tabBarInactiveTintColor: 'gray',
        headerShown: false,
        tabBarStyle: { paddingBottom: 5, paddingTop: 5, height: 60 },
        tabBarLabelStyle: { fontSize: 12, fontWeight: '500' },
      })}
    >
      <Tab.Screen name="Home" component={HomeScreen} options={{ tabBarLabel: 'Inicio' }} />

      {tienePermiso('escanear') && (
        <Tab.Screen name="Scan" component={ScanScreen} options={{ tabBarLabel: 'Escanear' }} />
      )}

      {tienePermiso('inventario') && (
        <Tab.Screen name="Inventory" component={InventoryScreen} options={{ tabBarLabel: 'Inventario' }} />
      )}

      <Tab.Screen name="Notifications" component={NotificacionesScreen} options={{ tabBarLabel: 'Notificaciones' }} />
      <Tab.Screen name="Profile" component={ProfileScreen} options={{ tabBarLabel: 'Perfil' }} />
    </Tab.Navigator>
  );
}

// ── Wrappers con control de permisos (definidos FUERA del render) ──────────
// Se usan refs globales para acceder a los permisos desde los wrappers
const permisosRef = { current: [] };

function PickingWrapper(props) {
  return permisosRef.current.includes('picking')
    ? <PickingScreen {...props} />
    : <SinPermisoScreen {...props} permiso="Picking" />;
}

function ReportsWrapper(props) {
  return permisosRef.current.includes('reportes')
    ? <ReportsScreen {...props} />
    : <SinPermisoScreen {...props} permiso="Reportar Problemas" />;
}

// ── App principal ──────────────────────────────────────────────────────────
export default function App() {
  const [permisos, setPermisos] = useState([]);

  useEffect(() => {
    const cargarPermisos = async () => {
      try {
        const raw = await AsyncStorage.getItem('currentUser');
        if (raw) {
          const user = JSON.parse(raw);
          const p = user.permisos ?? [];
          setPermisos(p);
          permisosRef.current = p; // sincronizar ref
        }
      } catch {}
    };
    cargarPermisos();
  }, []);

  // Mantener ref sincronizada con el estado
  permisosRef.current = permisos;

  return (
    <SafeAreaProvider>
      <NavigationContainer>
        <Stack.Navigator
          initialRouteName="Login"
          screenOptions={{
            headerStyle:      { backgroundColor: '#2563eb' },
            headerTintColor:  '#fff',
            headerTitleStyle: { fontWeight: 'bold' },
          }}
        >
          {/* Auth */}
          <Stack.Screen name="Login"            component={LoginScreen}             options={{ headerShown: false }} />
          <Stack.Screen name="CrearCuenta"       component={CrearCuentaScreen}       options={{ headerShown: false }} />
          <Stack.Screen name="RecuperarPassword" component={RecuperarPasswordScreen} options={{ headerShown: false }} />

          {/* Main — los permisos se pasan como parámetro de ruta */}
          <Stack.Screen
            name="MainTabs"
            component={MainTabs}
            options={{ headerShown: false }}
            initialParams={{ permisos }}
          />

          {/* Pantallas adicionales con guard de permisos via wrapper */}
          <Stack.Screen
            name="Picking"
            component={PickingWrapper}
            options={{ title: 'Gestión de Picking', headerBackTitle: 'Atrás' }}
          />

          <Stack.Screen
            name="Reports"
            component={ReportsWrapper}
            options={{ headerShown: false }}
          />

          <Stack.Screen
            name="ProductDetail"
            component={ProductDetailScreen}
            options={{ headerShown: false }}
          />
        </Stack.Navigator>
      </NavigationContainer>
    </SafeAreaProvider>
  );
}