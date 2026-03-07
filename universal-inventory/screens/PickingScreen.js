import React, { useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  SafeAreaView, Alert, Modal, TextInput
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';

const tareasIniciales = {
  activas: [
    {
      id: 'PIC-001',
      prioridad: 'ALTA',
      estado: 'pendiente',
      producto: 'Laptop HP EliteBook 840 G8',
      sku: 'LPT-HP-001',
      ubicacion: 'A-12-03',
      cantidadRequerida: 5,
      cantidadRecolectada: 0,
      fechaLimite: '2026-03-08T18:00:00',
      cliente: 'Ventas Corporativas',
      notas: 'Entregar en oficina 305',
    },
    {
      id: 'PIC-002',
      prioridad: 'MEDIA',
      estado: 'en_progreso',
      producto: 'Monitor Dell UltraSharp 24"',
      sku: 'MON-DL-002',
      ubicacion: 'B-05-01',
      cantidadRequerida: 3,
      cantidadRecolectada: 2,
      fechaLimite: '2026-03-09T12:00:00',
      cliente: 'Soporte Técnico',
      notas: 'Monitores para área de desarrollo',
    }
  ],
  completadas: []
};

const getPrioridadColor = p => ({ ALTA: '#e74c3c', MEDIA: '#f39c12', BAJA: '#3498db' }[p] || '#95a5a6');
const getEstadoColor = e => ({ pendiente: '#95a5a6', en_progreso: '#f39c12', completada: '#2ecc71' }[e] || '#95a5a6');
const getEstadoTexto = e => ({ pendiente: 'Pendiente', en_progreso: 'En Progreso', completada: 'Completada' }[e] || e);

const getTimeRemaining = (fechaLimite) => {
  const diff = new Date(fechaLimite) - new Date();
  if (diff < 0) return 'Vencida';
  const hours = Math.floor(diff / 3600000);

  return hours < 24
    ? `${hours}h restantes`
    : `${Math.floor(hours / 24)}d restantes`;
};

const PickingScreen = ({ navigation }) => {

  const [activeTab, setActiveTab] = useState('activas');
  const [tasks, setTasks] = useState(tareasIniciales);
  const [selectedTask, setSelectedTask] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [collectedQuantity, setCollectedQuantity] = useState('');

  const handleStartTask = useCallback((taskId) => {

    setTasks(prev => ({
      ...prev,
      activas: prev.activas.map(t =>
        t.id === taskId
          ? { ...t, estado: 'en_progreso' }
          : t
      )
    }));

  }, []);

  const handleOpenCollectModal = useCallback((task) => {

    setSelectedTask(task);
    setCollectedQuantity(task.cantidadRecolectada.toString());
    setModalVisible(true);

  }, []);

  const handleCollectItems = useCallback(() => {

    if (!selectedTask) return;

    const quantity = parseInt(collectedQuantity, 10);

    if (isNaN(quantity) || quantity < 0) {

      Alert.alert('Error', 'Ingresa una cantidad válida');
      return;

    }

    if (quantity > selectedTask.cantidadRequerida) {

      Alert.alert(
        'Error',
        `La cantidad no puede exceder ${selectedTask.cantidadRequerida}`
      );
      return;

    }

    const isComplete = quantity === selectedTask.cantidadRequerida;

    if (isComplete) {

      const completedTask = {
        ...selectedTask,
        cantidadRecolectada: quantity,
        estado: 'completada',
        fechaCompletada: new Date().toISOString(),
        completadoPor: 'Usuario Actual'
      };

      setTasks(prev => ({
        activas: prev.activas.filter(t => t.id !== selectedTask.id),
        completadas: [completedTask, ...prev.completadas]
      }));

      Alert.alert(
        '¡Completado!',
        `Tarea ${selectedTask.id} completada correctamente`
      );

    } else {

      setTasks(prev => ({
        ...prev,
        activas: prev.activas.map(t =>
          t.id === selectedTask.id
            ? { ...t, cantidadRecolectada: quantity, estado: 'en_progreso' }
            : t
        )
      }));

      Alert.alert(
        'Registrado',
        `${quantity} de ${selectedTask.cantidadRequerida} unidades registradas`
      );

    }

    setModalVisible(false);
    setSelectedTask(null);

  }, [selectedTask, collectedQuantity]);

  const renderTaskItem = ({ item }) => (

    <View style={styles.taskCard}>

      <View style={styles.taskHeader}>

        <View style={styles.taskIdContainer}>
          <Text style={styles.taskId}>{item.id}</Text>

          <View style={[
            styles.priorityBadge,
            { backgroundColor: getPrioridadColor(item.prioridad) }
          ]}>
            <Text style={styles.badgeText}>{item.prioridad}</Text>
          </View>
        </View>

        <View style={[
          styles.statusBadge,
          { backgroundColor: getEstadoColor(item.estado) }
        ]}>
          <Text style={styles.badgeText}>
            {getEstadoTexto(item.estado)}
          </Text>
        </View>

      </View>

      <Text style={styles.productName}>{item.producto}</Text>

      <Text style={styles.productSku}>
        SKU: {item.sku}
      </Text>

      <View style={styles.progressBar}>

        <View
          style={[
            styles.progressFill,
            {
              width: `${(item.cantidadRecolectada / item.cantidadRequerida) * 100}%`,
              backgroundColor: getPrioridadColor(item.prioridad)
            }
          ]}
        />

      </View>

      <View style={styles.footerRow}>

        <Text style={styles.timeText}>
          {getTimeRemaining(item.fechaLimite)}
        </Text>

        <Text style={styles.clientText}>
          {item.cliente}
        </Text>

      </View>

      <TouchableOpacity
        style={styles.collectButton}
        onPress={() => handleOpenCollectModal(item)}
      >

        <MaterialIcons name="add-shopping-cart" size={16} color="white" />

        <Text style={styles.collectButtonText}>
          Registrar
        </Text>

      </TouchableOpacity>

    </View>

  );

  return (

    <SafeAreaView style={styles.container}>

      <View style={styles.header}>

        <Text style={styles.title}>
          Picking
        </Text>

      </View>

      <FlatList
        data={tasks.activas}
        renderItem={renderTaskItem}
        keyExtractor={item => item.id}
      />

      <Modal
        visible={modalVisible}
        transparent
        animationType="slide"
      >

        <View style={styles.modalContainer}>

          <View style={styles.modalContent}>

            {selectedTask && (

              <>
                <Text style={styles.modalTitle}>
                  Registrar Recolección
                </Text>

                <TextInput
                  style={styles.modalInput}
                  placeholder={`Máx: ${selectedTask.cantidadRequerida}`}
                  value={collectedQuantity}
                  onChangeText={setCollectedQuantity}
                  keyboardType="numeric"
                />

                <TouchableOpacity
                  style={styles.modalButton}
                  onPress={handleCollectItems}
                >
                  <Text style={styles.btnText}>
                    Registrar
                  </Text>
                </TouchableOpacity>

              </>

            )}

          </View>

        </View>

      </Modal>

    </SafeAreaView>

  );

};

const styles = StyleSheet.create({

container:{flex:1,backgroundColor:'#f5f5f5'},

header:{
flexDirection:'row',
justifyContent:'space-between',
alignItems:'center',
padding:20,
backgroundColor:'white'
},

title:{fontSize:24,fontWeight:'bold'},

taskCard:{
backgroundColor:'white',
padding:15,
margin:10,
borderRadius:10
},

taskHeader:{
flexDirection:'row',
justifyContent:'space-between'
},

taskId:{fontWeight:'bold'},

priorityBadge:{
padding:5,
borderRadius:10
},

statusBadge:{
padding:5,
borderRadius:10
},

badgeText:{color:'white',fontSize:10},

productName:{fontWeight:'bold',marginTop:5},

productSku:{color:'#777'},

progressBar:{
height:5,
backgroundColor:'#eee',
borderRadius:5,
marginVertical:10
},

progressFill:{
height:'100%',
borderRadius:5
},

footerRow:{
flexDirection:'row',
justifyContent:'space-between'
},

collectButton:{
backgroundColor:'#f39c12',
padding:10,
borderRadius:6,
flexDirection:'row',
alignItems:'center',
justifyContent:'center',
marginTop:10
},

collectButtonText:{
color:'white',
marginLeft:6
},

modalContainer:{
flex:1,
justifyContent:'center',
alignItems:'center',
backgroundColor:'rgba(0,0,0,0.5)'
},

modalContent:{
backgroundColor:'white',
padding:20,
borderRadius:10,
width:'80%'
},

modalTitle:{
fontSize:18,
fontWeight:'bold',
marginBottom:10
},

modalInput:{
borderWidth:1,
borderColor:'#ccc',
padding:10,
borderRadius:6,
marginBottom:10
},

modalButton:{
backgroundColor:'#2ecc71',
padding:10,
borderRadius:6,
alignItems:'center'
},

btnText:{
color:'white',
fontWeight:'bold'
}

});

export default PickingScreen;