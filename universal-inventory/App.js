// App.js
import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons as Icon } from '@expo/vector-icons';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Importar pantallas
import LoginScreen          from './screens/LoginScreen';
import RecuperarPasswordScreen from './screens/RecuperarPasswordScreen';
import HomeScreen           from './screens/HomeScreen';
import ScanScreen           from './screens/ScanScreen';
import InventoryScreen      from './screens/InventoryScreen';
import NotificacionesScreen from './screens/NotificacionesScreen';
import ProfileScreen        from './screens/ProfileScreen';
import PickingScreen        from './screens/PickingScreen';
import ReportsScreen        from './screens/ReportsScreen';
import ProductDetailScreen  from './screens/ProductDetailScreen';

const Stack = createStackNavigator();
const Tab   = createBottomTabNavigator();

// ── Tabs dinámicas según permisos ─────────────────────────────────────────
function MainTabs({ permisos = [] }) {
  const tienePermiso = (p) => permisos.includes(p);

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
        headerShown: false,
        tabBarStyle: { paddingBottom: 5, paddingTop: 5, height: 60 },
        tabBarLabelStyle: { fontSize: 12, fontWeight: '500' },
      })}
    >
      {/* Inicio — siempre visible */}
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{ tabBarLabel: 'Inicio' }}
      />

      {/* Escanear — permiso: escanear */}
      {tienePermiso('escanear') && (
        <Tab.Screen
          name="Scan"
          component={ScanScreen}
          options={{ tabBarLabel: 'Escanear' }}
        />
      )}

      {/* Inventario — permiso: inventario */}
      {tienePermiso('inventario') && (
        <Tab.Screen
          name="Inventory"
          component={InventoryScreen}
          options={{ tabBarLabel: 'Inventario' }}
        />
      )}

      {/* Notificaciones — siempre visible */}
      <Tab.Screen
        name="Notifications"
        component={NotificacionesScreen}
        options={{ tabBarLabel: 'Notificaciones' }}
      />

      {/* Perfil — siempre visible */}
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{ tabBarLabel: 'Perfil' }}
      />
    </Tab.Navigator>
  );
}

// ── App principal ──────────────────────────────────────────────────────────
export default function App() {
  const [permisos, setPermisos] = useState([]);

  // Cargar permisos del usuario en sesión
  useEffect(() => {
    const cargarPermisos = async () => {
      try {
        const raw = await AsyncStorage.getItem('currentUser');
        if (raw) {
          const user = JSON.parse(raw);
          setPermisos(user.permisos ?? []);
        }
      } catch {}
    };
    cargarPermisos();
  }, []);

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
          <Stack.Screen name="Login"            component={LoginScreen}            options={{ headerShown: false }} />
          <Stack.Screen name="RecuperarPassword" component={RecuperarPasswordScreen} options={{ headerShown: false }} />

          {/* Main — pasa permisos a las tabs */}
          <Stack.Screen name="MainTabs" options={{ headerShown: false }}>
            {() => <MainTabs permisos={permisos} />}
          </Stack.Screen>

          {/* Pantallas adicionales — verifican permiso antes de mostrar */}
          <Stack.Screen
            name="Picking"
            options={{ title: 'Gestión de Picking', headerBackTitle: 'Atrás' }}
          >
            {(props) =>
              permisos.includes('picking')
                ? <PickingScreen {...props} />
                : <SinPermisoScreen {...props} permiso="Picking" />
            }
          </Stack.Screen>

          <Stack.Screen
            name="Reports"
            options={{ headerShown: false }}
          >
            {(props) =>
              permisos.includes('reportes')
                ? <ReportsScreen {...props} />
                : <SinPermisoScreen {...props} permiso="Reportar Problemas" />
            }
          </Stack.Screen>

          <Stack.Screen name="ProductDetail" component={ProductDetailScreen} options={{ headerShown: false }} />
        </Stack.Navigator>
      </NavigationContainer>
    </SafeAreaProvider>
  );
}

// ── Pantalla de sin permiso ────────────────────────────────────────────────
function SinPermisoScreen({ navigation, permiso }) {
  return (
    <SafeAreaProvider>
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
    </SafeAreaProvider>
  );
}

// Imports necesarios para SinPermisoScreen
import { View, Text, TouchableOpacity } from 'react-native';