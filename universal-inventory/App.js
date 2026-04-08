// App.js
import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons as Icon } from '@expo/vector-icons';
import { SafeAreaProvider } from 'react-native-safe-area-context';

// Importar todas las pantallas
import LoginScreen from './screens/LoginScreen';
import CrearCuentaScreen from './screens/CrearCuentaScreen';
import RecuperarPasswordScreen from './screens/RecuperarPasswordScreen';
import HomeScreen from './screens/HomeScreen';
import ScanScreen from './screens/ScanScreen';
import InventoryScreen from './screens/InventoryScreen';
import NotificacionesScreen from './screens/NotificacionesScreen';
import ProfileScreen from './screens/ProfileScreen';
import PickingScreen from './screens/PickingScreen';
import ReportsScreen from './screens/ReportsScreen';
import ProductDetailScreen from './screens/ProductDetailScreen';

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();

// Configuración de las tabs principales
function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName;
          
          switch(route.name) {
            case 'Home':
              iconName = focused ? 'home' : 'home-outline';
              break;
            case 'Scan':
              iconName = focused ? 'scan' : 'scan-outline';
              break;
            case 'Inventory':
              iconName = focused ? 'cube' : 'cube-outline';
              break;
            case 'Notifications':
              iconName = focused ? 'notifications' : 'notifications-outline';
              break;
            case 'Profile':
              iconName = focused ? 'person' : 'person-outline';
              break;
            default:
              iconName = 'home-outline';
          }
          
          return <Icon name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#2563eb',
        tabBarInactiveTintColor: 'gray',
        headerShown: false,
        tabBarStyle: {
          paddingBottom: 5,
          paddingTop: 5,
          height: 60,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '500',
        },
      })}
    >
      <Tab.Screen 
        name="Home" 
        component={HomeScreen} 
        options={{ tabBarLabel: 'Inicio' }}
      />
      <Tab.Screen 
        name="Scan" 
        component={ScanScreen} 
        options={{ tabBarLabel: 'Escanear' }}
      />
      <Tab.Screen 
        name="Inventory" 
        component={InventoryScreen} 
        options={{ tabBarLabel: 'Inventario' }}
      />
      <Tab.Screen 
        name="Notifications" 
        component={NotificacionesScreen} 
        options={{ tabBarLabel: 'Notificaciones' }}
      />
      <Tab.Screen 
        name="Profile" 
        component={ProfileScreen} 
        options={{ tabBarLabel: 'Perfil' }}
      />
    </Tab.Navigator>
  );
}

// Configuración principal de navegación
export default function App() {
  return (
    <SafeAreaProvider>
      <NavigationContainer>
      <Stack.Navigator 
        initialRouteName="Login"
        screenOptions={{
          headerStyle: {
            backgroundColor: '#2563eb',
          },
          headerTintColor: '#fff',
          headerTitleStyle: {
            fontWeight: 'bold',
          },
        }}
      >
        {/* Pantallas de autenticación */}
        <Stack.Screen 
          name="Login" 
          component={LoginScreen} 
          options={{ headerShown: false }}
        />
        <Stack.Screen 
          name="CrearCuenta" 
          component={CrearCuentaScreen} 
          options={{ headerShown: false }}
        />
        <Stack.Screen 
          name="RecuperarPassword" 
          component={RecuperarPasswordScreen} 
          options={{ headerShown: false }}
        />
        
        {/* Pantalla principal con tabs */}
        <Stack.Screen 
          name="MainTabs" 
          component={MainTabs} 
          options={{ headerShown: false }}
        />
        
        {/* Pantallas adicionales */}
        <Stack.Screen 
          name="Picking" 
          component={PickingScreen} 
          options={{ 
            title: 'Gestión de Picking',
            headerBackTitle: 'Atrás',
          }}
        />
        
        <Stack.Screen 
          name="Reports" 
          component={ReportsScreen} 
          options={{ 
            title: 'Reportar Problema',
            headerBackTitle: 'Atrás',
          }}
        />

        <Stack.Screen 
          name="ProductDetail" 
          component={ProductDetailScreen} 
          options={{ headerShown: false }}  // ← agrega esto
        />
      </Stack.Navigator>
    </NavigationContainer>
  </SafeAreaProvider>
  );
}