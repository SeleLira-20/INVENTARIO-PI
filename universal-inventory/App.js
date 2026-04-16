// App.js
import React, { useState, useEffect, createContext, useContext } from 'react';
import { View, Text, TouchableOpacity, ActivityIndicator } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons as Icon } from '@expo/vector-icons';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import AsyncStorage from '@react-native-async-storage/async-storage';

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

// ── Contexto global de permisos ───────────────────────────────────────────
export const PermisosContext = createContext({
  permisos: [],
  setPermisos: () => {},
});

const Stack = createStackNavigator();
const Tab   = createBottomTabNavigator();

// ── Pantalla sin permiso ──────────────────────────────────────────────────
function SinPermisoScreen({ navigation, permiso }) {
  return (
    <View style={{ flex:1, justifyContent:'center', alignItems:'center', backgroundColor:'#f8fafc', padding:30 }}>
      <Icon name="lock-closed" size={64} color="#94a3b8" />
      <Text style={{ fontSize:20, fontWeight:'800', color:'#1e293b', marginTop:16, marginBottom:8 }}>
        Acceso restringido
      </Text>
      <Text style={{ fontSize:14, color:'#64748b', textAlign:'center', lineHeight:20 }}>
        No tienes permiso para{'\n'}
        <Text style={{ fontWeight:'700' }}>{permiso}</Text>.{'\n\n'}
        Contacta al administrador.
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
function MainTabs() {
  const { permisos } = useContext(PermisosContext);

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          const icons = {
            Home:          focused ? 'home'          : 'home-outline',
            Scan:          focused ? 'scan'          : 'scan-outline',
            Inventory:     focused ? 'cube'          : 'cube-outline',
            Notifications: focused ? 'notifications' : 'notifications-outline',
            Profile:       focused ? 'person'        : 'person-outline',
          };
          return <Icon name={icons[route.name] || 'home-outline'} size={size} color={color} />;
        },
        tabBarActiveTintColor:   '#2563eb',
        tabBarInactiveTintColor: 'gray',
        headerShown:      false,
        tabBarStyle:      { paddingBottom: 5, paddingTop: 5, height: 60 },
        tabBarLabelStyle: { fontSize: 12, fontWeight: '500' },
      })}
    >
      <Tab.Screen name="Home" component={HomeScreen} options={{ tabBarLabel: 'Inicio' }} />

      {permisos.includes('escanear') && (
        <Tab.Screen name="Scan" component={ScanScreen} options={{ tabBarLabel: 'Escanear' }} />
      )}

      {permisos.includes('inventario') && (
        <Tab.Screen name="Inventory" component={InventoryScreen} options={{ tabBarLabel: 'Inventario' }} />
      )}

      <Tab.Screen name="Notifications" component={NotificacionesScreen} options={{ tabBarLabel: 'Notificaciones' }} />
      <Tab.Screen name="Profile"       component={ProfileScreen}        options={{ tabBarLabel: 'Perfil' }} />
    </Tab.Navigator>
  );
}

// ── App principal ─────────────────────────────────────────────────────────
export default function App() {
  const [permisos, setPermisos] = useState([]);
  const [cargando, setCargando] = useState(true);

  useEffect(() => {
    AsyncStorage.getItem('currentUser')
      .then(raw => {
        if (raw) {
          const user = JSON.parse(raw);
          const p = normalizePermisos(user.permisos);
          setPermisos(p);
        }
      })
      .catch(() => {})
      .finally(() => setCargando(false));
  }, []);

  const normalizePermisos = (p) => {
    if (!p) return [];
    if (Array.isArray(p)) return p.map(x => x.trim().toLowerCase()).filter(Boolean);
    return p.split(',').map(x => x.trim().toLowerCase()).filter(Boolean);
  };

  const handleLogin = (nuevosPermisos) => {
    setPermisos(normalizePermisos(nuevosPermisos));
  };

  if (cargando) {
    return (
      <View style={{ flex:1, justifyContent:'center', alignItems:'center', backgroundColor:'#1e2d4a' }}>
        <ActivityIndicator size="large" color="#fff" />
      </View>
    );
  }

  return (
    <SafeAreaProvider>
      <PermisosContext.Provider value={{ permisos, setPermisos: (p) => setPermisos(normalizePermisos(p)) }}>
        <NavigationContainer>
          <Stack.Navigator
            initialRouteName="Login"
            screenOptions={{
              headerStyle:      { backgroundColor: '#2563eb' },
              headerTintColor:  '#fff',
              headerTitleStyle: { fontWeight: 'bold' },
            }}
          >
            <Stack.Screen name="Login" options={{ headerShown: false }}>
              {(props) => <LoginScreen {...props} onLogin={handleLogin} />}
            </Stack.Screen>

            <Stack.Screen name="CrearCuenta"       component={CrearCuentaScreen}       options={{ headerShown: false }} />
            <Stack.Screen name="RecuperarPassword" component={RecuperarPasswordScreen} options={{ headerShown: false }} />
            <Stack.Screen name="MainTabs"          component={MainTabs}                options={{ headerShown: false }} />

            <Stack.Screen name="Picking" options={{ headerShown: false }}>
              {(props) =>
                permisos.includes('picking')
                  ? <PickingScreen {...props} />
                  : <SinPermisoScreen {...props} permiso="Tareas de Picking" />
              }
            </Stack.Screen>

            <Stack.Screen name="Reports" options={{ headerShown: false }}>
              {(props) =>
                permisos.includes('reportes')
                  ? <ReportsScreen {...props} />
                  : <SinPermisoScreen {...props} permiso="Reportar Problemas" />
              }
            </Stack.Screen>

            <Stack.Screen name="ProductDetail" component={ProductDetailScreen} options={{ headerShown: false }} />
          </Stack.Navigator>
        </NavigationContainer>
      </PermisosContext.Provider>
    </SafeAreaProvider>
  );
}