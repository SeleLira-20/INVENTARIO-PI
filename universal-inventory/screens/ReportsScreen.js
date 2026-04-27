import React, { useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, TextInput, Alert, Image, StatusBar,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
const API_BASE = 'https://inventario-pi-1.onrender.com';

const ReportsScreen = ({ navigation, route }) => {
  const { product } = route.params || {};
  const insets = useSafeAreaInsets();

  const [formData, setFormData] = useState({
    tipoProblema: '',
    sku: product?.sku || '',
    descripcion: '',
    ubicacion: product?.ubicacion || '',
    urgencia: 'media',
    foto: null,
  });
  const [isSubmitting, setIsSubmitting] = useState(false);

  const tiposProblema = [
    { id: 'stock_incorrecto',     label: 'Stock incorrecto',     icon: 'inventory' },
    { id: 'ubicacion_incorrecta', label: 'Ubicación incorrecta', icon: 'location-off' },
    { id: 'producto_daniado',     label: 'Producto dañado',      icon: 'report-problem' },
    { id: 'etiqueta_daniada',     label: 'Etiqueta dañada',      icon: 'label-off' },
    { id: 'falta_producto',       label: 'Falta producto',       icon: 'remove-shopping-cart' },
    { id: 'otro',                 label: 'Otro',                 icon: 'help' },
  ];

  const urgencias = [
    { id: 'baja',  label: 'Baja',  color: '#3498db', apiVal: 'BAJA'  },
    { id: 'media', label: 'Media', color: '#f39c12', apiVal: 'MEDIA' },
    { id: 'alta',  label: 'Alta',  color: '#e74c3c', apiVal: 'ALTA'  },
  ];

  const pickImage = async () => {
    const { status } = await ImagePicker.requestCameraPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permiso denegado', 'Se necesita acceso a la cámara. Puedes habilitarlo en Configuración.');
      return;
    }
    const result = await ImagePicker.launchCameraAsync({
      allowsEditing: true, quality: 0.7, aspect: [4, 3],
    });
    if (!result.canceled && result.assets?.length > 0) {
      setFormData(prev => ({ ...prev, foto: result.assets[0].uri }));
    }
  };

  const pickFromGallery = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permiso denegado', 'Se necesita acceso a la galería');
      return;
    }
    const result = await ImagePicker.launchImageLibraryAsync({
      allowsEditing: true, quality: 0.7, aspect: [4, 3],
    });
    if (!result.canceled && result.assets?.length > 0) {
      setFormData(prev => ({ ...prev, foto: result.assets[0].uri }));
    }
  };

  const handlePhotoOptions = () => {
    Alert.alert('Adjuntar Foto', 'Elige una opción', [
      { text: 'Cancelar', style: 'cancel' },
      { text: 'Tomar foto', onPress: pickImage },
      { text: 'Desde galería', onPress: pickFromGallery },
    ]);
  };

  const validateForm = () => {
    if (!formData.tipoProblema) {
      Alert.alert('Error', 'Selecciona el tipo de problema');
      return false;
    }
    if (!formData.descripcion.trim()) {
      Alert.alert('Error', 'Describe el problema');
      return false;
    }
    if (formData.descripcion.trim().length < 10) {
      Alert.alert('Error', 'La descripción debe tener al menos 10 caracteres');
      return false;
    }
    return true;
  };

  const handleSubmit = async () => {
    if (!validateForm()) return;
    setIsSubmitting(true);

    try {
      const urgenciaSeleccionada = urgencias.find(u => u.id === formData.urgencia);
      const nivelUrgencia = urgenciaSeleccionada?.apiVal ?? 'MEDIA';

      let id_producto = null;
      if (formData.sku.trim()) {
        try {
          const prodResp = await fetch(`${API_URL}/v1/productos/`);
          const prodData = await prodResp.json();
          const prod = (prodData.productos ?? []).find(
            p => p.sku.toLowerCase() === formData.sku.trim().toLowerCase()
          );
          if (prod) id_producto = prod.id_producto;
        } catch (_) {}
      }

      const payload = {
        tipo_problema:      formData.tipoProblema,
        descripcion:        formData.descripcion.trim(),
        id_usuario_reporta: 1,
        nivel_urgencia:     nivelUrgencia,
        ...(id_producto && { id_producto }),
      };

      const response = await fetch(`${API_URL}/v1/incidentes/`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.detail ?? 'Error al enviar el reporte');
      }

      const folio = `INC-${data.incidente?.id_incidente ?? Date.now().toString().slice(-6)}`;
      Alert.alert(
        'Reporte Enviado',
        `Tu reporte fue registrado correctamente.\n\nFolio: ${folio}\nTipo: ${formData.tipoProblema}\nUrgencia: ${nivelUrgencia}`,
        [{ text: 'Aceptar', onPress: () => navigation.goBack() }]
      );

    } catch (error) {
      Alert.alert(
        'Error al enviar',
        `No se pudo enviar el reporte.\n\n${error.message}\n\nVerifica que estés conectado a la misma red WiFi.`,
        [{ text: 'Reintentar', onPress: handleSubmit }, { text: 'Cancelar', style: 'cancel' }]
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const update = (key, value) => setFormData(prev => ({ ...prev, [key]: value }));

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#2563eb" />

      {/* Header */}
      <View style={[styles.header, { paddingTop: insets.top + 10 }]}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <MaterialIcons name="arrow-back" size={22} color="white" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Reportar Problema</Text>
        <View style={{ width: 36 }} />
      </View>

      <ScrollView
        contentContainerStyle={[styles.scrollContent, { paddingBottom: insets.bottom + 20 }]}
        showsVerticalScrollIndicator={false}
      >
        {/* Tipo de Problema */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            Tipo de Problema <Text style={styles.required}>*</Text>
          </Text>
          <View style={styles.problemTypes}>
            {tiposProblema.map(tipo => {
              const selected = formData.tipoProblema === tipo.id;
              return (
                <TouchableOpacity
                  key={tipo.id}
                  style={[styles.problemTypeCard, selected && styles.problemTypeSelected]}
                  onPress={() => update('tipoProblema', tipo.id)}
                >
                  <MaterialIcons name={tipo.icon} size={24} color={selected ? 'white' : '#3498db'} />
                  <Text style={[styles.problemTypeText, selected && styles.problemTypeTextSelected]}>
                    {tipo.label}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* SKU */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>SKU del Producto (opcional)</Text>
          <View style={styles.inputContainer}>
            <MaterialIcons name="qr-code" size={20} color="#7f8c8d" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ej: LPT-HP-001"
              value={formData.sku}
              onChangeText={v => update('sku', v)}
              autoCapitalize="characters"
            />
          </View>
        </View>

        {/* Ubicación */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Ubicación (opcional)</Text>
          <View style={styles.inputContainer}>
            <MaterialIcons name="location-on" size={20} color="#7f8c8d" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Ej: A-12-03"
              value={formData.ubicacion}
              onChangeText={v => update('ubicacion', v)}
            />
          </View>
        </View>

        {/* Descripción */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>
            Descripción <Text style={styles.required}>*</Text>
          </Text>
          <View style={styles.textAreaContainer}>
            <TextInput
              style={styles.textArea}
              placeholder="Describe el problema con detalle (mínimo 10 caracteres)..."
              value={formData.descripcion}
              onChangeText={v => update('descripcion', v)}
              multiline
              numberOfLines={4}
              maxLength={200}
              textAlignVertical="top"
            />
            <Text style={styles.charCount}>{formData.descripcion.length}/200</Text>
          </View>
        </View>

        {/* Urgencia */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Nivel de Urgencia</Text>
          <View style={styles.urgencyContainer}>
            {urgencias.map(urg => {
              const selected = formData.urgencia === urg.id;
              return (
                <TouchableOpacity
                  key={urg.id}
                  style={[
                    styles.urgencyButton,
                    { borderColor: urg.color },
                    selected && { backgroundColor: urg.color },
                  ]}
                  onPress={() => update('urgencia', urg.id)}
                >
                  <Text style={[styles.urgencyText, { color: selected ? 'white' : urg.color }]}>
                    {urg.label}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* Foto */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Adjuntar Foto (opcional)</Text>
          <TouchableOpacity style={styles.photoButton} onPress={handlePhotoOptions}>
            <MaterialIcons name="add-a-photo" size={24} color="#3498db" />
            <Text style={styles.photoButtonText}>
              {formData.foto ? 'Cambiar foto' : 'Adjuntar foto'}
            </Text>
          </TouchableOpacity>
          {formData.foto && (
            <View style={styles.previewContainer}>
              <Image source={{ uri: formData.foto }} style={styles.imagePreview} />
              <TouchableOpacity style={styles.removePhoto} onPress={() => update('foto', null)}>
                <MaterialIcons name="close" size={18} color="white" />
              </TouchableOpacity>
            </View>
          )}
        </View>

        {/* Nota */}
        <View style={styles.noteContainer}>
          <MaterialIcons name="info" size={18} color="#3498db" />
          <Text style={styles.noteText}>
            Los campos con <Text style={styles.required}>*</Text> son obligatorios
          </Text>
        </View>

        {/* Botón enviar */}
        <TouchableOpacity
          style={[styles.submitButton, isSubmitting && { opacity: 0.6 }]}
          onPress={handleSubmit}
          disabled={isSubmitting}
        >
          <MaterialIcons name="send" size={20} color="white" />
          <Text style={styles.submitButtonText}>
            {isSubmitting ? ' Enviando...' : ' Enviar Reporte'}
          </Text>
        </TouchableOpacity>

      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f1f5f9' },
  header: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    backgroundColor: '#2563eb', paddingHorizontal: 16, paddingVertical: 14,
  },
  backBtn: {
    width: 36, height: 36, borderRadius: 10,
    backgroundColor: 'rgba(255,255,255,0.18)',
    justifyContent: 'center', alignItems: 'center',
  },
  headerTitle: {
    flex: 1, fontSize: 17, fontWeight: '700',
    color: 'white', textAlign: 'center', marginHorizontal: 8,
  },
  scrollContent: { padding: 15 },
  section: {
    backgroundColor: 'white', borderRadius: 14,
    padding: 15, marginBottom: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05, shadowRadius: 4, elevation: 2,
  },
  sectionTitle: { fontSize: 15, fontWeight: 'bold', color: '#2c3e50', marginBottom: 10 },
  required: { color: '#e74c3c' },
  problemTypes: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  problemTypeCard: {
    width: '48%', backgroundColor: '#f5f5f5', padding: 12,
    borderRadius: 8, alignItems: 'center', marginBottom: 10,
    borderWidth: 1, borderColor: '#ecf0f1',
  },
  problemTypeSelected: { backgroundColor: '#3498db', borderColor: '#3498db' },
  problemTypeText: { marginTop: 5, fontSize: 12, textAlign: 'center', color: '#34495e' },
  problemTypeTextSelected: { color: 'white' },
  inputContainer: {
    flexDirection: 'row', alignItems: 'center',
    borderWidth: 1, borderColor: '#bdc3c7',
    borderRadius: 8, backgroundColor: '#f9f9f9',
  },
  inputIcon: { padding: 10 },
  input: { flex: 1, paddingVertical: 12, paddingRight: 10, fontSize: 15 },
  textAreaContainer: {
    borderWidth: 1, borderColor: '#bdc3c7',
    borderRadius: 8, backgroundColor: '#f9f9f9',
  },
  textArea: { padding: 12, fontSize: 15, minHeight: 100 },
  charCount: { textAlign: 'right', padding: 5, color: '#95a5a6', fontSize: 11 },
  urgencyContainer: { flexDirection: 'row', justifyContent: 'space-between', gap: 8 },
  urgencyButton: {
    flex: 1, padding: 12, borderRadius: 8,
    borderWidth: 2, alignItems: 'center',
  },
  urgencyText: { fontWeight: 'bold', fontSize: 14 },
  photoButton: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
    padding: 15, borderWidth: 2, borderColor: '#3498db',
    borderRadius: 8, borderStyle: 'dashed', backgroundColor: '#ebf5ff',
  },
  photoButtonText: { marginLeft: 8, color: '#3498db', fontSize: 15, fontWeight: '600' },
  previewContainer: { position: 'relative', marginTop: 12, alignItems: 'center' },
  imagePreview: { width: '100%', height: 200, borderRadius: 8 },
  removePhoto: {
    position: 'absolute', top: 8, right: 8,
    backgroundColor: '#e74c3c', borderRadius: 14,
    width: 28, height: 28, justifyContent: 'center', alignItems: 'center',
  },
  noteContainer: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#ebf5ff', padding: 12,
    borderRadius: 8, marginBottom: 15,
  },
  noteText: { marginLeft: 8, color: '#2c3e50', fontSize: 13 },
  submitButton: {
    backgroundColor: '#e74c3c', padding: 16, borderRadius: 10,
    alignItems: 'center', flexDirection: 'row',
    justifyContent: 'center', marginBottom: 30,
  },
  submitButtonText: { color: 'white', fontSize: 17, fontWeight: 'bold' },
});

export default ReportsScreen;