import { FontAwesome5 } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import axios from 'axios';
import * as Haptics from 'expo-haptics';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Dimensions,
  FlatList,
  Modal,
  SafeAreaView,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';
import Animated, { FadeInDown, FadeInRight } from 'react-native-reanimated';

const { width } = Dimensions.get('window');

export default function CollectionReportScreen() {
  const router = useRouter();
  const [isLoading, setIsLoading] = useState(false);
  const [reports, setReports] = useState([]);
  const [filteredReports, setFilteredReports] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  
  const [fromDate, setFromDate] = useState(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)); // 7 days ago
  const [toDate, setToDate] = useState(new Date());
  const [showFromDatePicker, setShowFromDatePicker] = useState(false);
  const [showToDatePicker, setShowToDatePicker] = useState(false);
  
  const [summaryData, setSummaryData] = useState({
    totalAmount: 0,
    totalReceipts: 0,
    avgAmount: 0
  });
  
  const [showReceiptModal, setShowReceiptModal] = useState(false);
  const [selectedReceipt, setSelectedReceipt] = useState(null);
  
  useEffect(() => {
    fetchReports();
  }, [fromDate, toDate]);
  
  useEffect(() => {
    if (searchQuery.trim() === '') {
      setFilteredReports(reports);
    } else {
      const filtered = reports.filter(report => 
        report.student_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        report.admission_no.includes(searchQuery) ||
        report.receipt_id.includes(searchQuery)
      );
      setFilteredReports(filtered);
    }
  }, [searchQuery, reports]);
  
  useEffect(() => {
    if (reports.length > 0) {
      calculateSummary();
    }
  }, [reports]);
  
  const fetchReports = async () => {
    setIsLoading(true);
    try {
      const formattedFromDate = fromDate.toISOString().split('T')[0];
      const formattedToDate = toDate.toISOString().split('T')[0];
      
      const response = await axios.post(
        'https://dpsmushkipur.com/bine/api.php?task=collection_report',
        {
          from_date: formattedFromDate,
          to_date: formattedToDate
        }
      );
      
      if (response.data.status === 'success') {
        // Process the data - convert string amounts to numbers and handle nulls
        const processedData = response.data.data.map(item => ({
          ...item,
          paid_amount: item.paid_amount !== null ? 
                       parseFloat(item.paid_amount) : 
                       null
        }));
        
        setReports(processedData);
        setFilteredReports(processedData);
      } else {
        Alert.alert('Error', 'Failed to load reports');
      }
    } catch (error) {
      console.error('Error fetching reports:', error);
      Alert.alert('Error', 'An error occurred while fetching reports');
    } finally {
      setIsLoading(false);
    }
  };
  
  const calculateSummary = () => {
    // Calculate total collection amount (ignoring null values)
    const totalAmount = reports.reduce((sum, report) => {
      return sum + (report.paid_amount || 0);
    }, 0);
    
    // Count receipts with valid amounts
    const validReceipts = reports.filter(report => report.paid_amount !== null).length;
    
    // Calculate average amount
    const avgAmount = validReceipts > 0 ? totalAmount / validReceipts : 0;
    
    setSummaryData({
      totalAmount,
      totalReceipts: reports.length,
      avgAmount
    });
  };
  
  const handleFromDateChange = (event, selectedDate) => {
    setShowFromDatePicker(false);
    if (selectedDate) {
      setFromDate(selectedDate);
    }
  };
  
  const handleToDateChange = (event, selectedDate) => {
    setShowToDatePicker(false);
    if (selectedDate) {
      setToDate(selectedDate);
    }
  };
  
  const handleReceiptPress = (receipt) => {
    setSelectedReceipt(receipt);
    setShowReceiptModal(true);
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
  };
  
  const viewFullReceipt = () => {
    setShowReceiptModal(false);
    router.push({
      pathname: '/ReceiptScreen',
      params: {
        receipt_id: selectedReceipt.receipt_id
      }
    });
  };
  
  // Prepare chart data
  const generateChartData = () => {
    // Group by date
    const dateGroups = {};
    reports.forEach(report => {
      const date = report.paid_date;
      if (!dateGroups[date]) {
        dateGroups[date] = {
          total: 0,
          count: 0
        };
      }
      
      dateGroups[date].total += (report.paid_amount || 0);
      dateGroups[date].count += 1;
    });
    
    // Convert to array and sort by date
    const sortedDates = Object.keys(dateGroups).sort();
    
    return {
      labels: sortedDates.map(date => {
        // Format as short date (e.g., "11 Oct")
        const [year, month, day] = date.split('-');
        return `${day} ${['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][parseInt(month) - 1]}`;
      }),
      datasets: [
        {
          data: sortedDates.map(date => dateGroups[date].total)
        }
      ]
    };
  };
  
  const renderReceiptItem = ({ item, index }) => {
    return (
      <Animated.View 
        entering={FadeInDown.delay(index * 50).springify()}
        style={styles.receiptItem}
      >
        <TouchableOpacity 
          style={styles.receiptCard}
          onPress={() => handleReceiptPress(item)}
          activeOpacity={0.7}
        >
          <View style={styles.receiptHeader}>
            <View style={styles.receiptInfo}>
              <Text style={styles.receiptId}>#{item.receipt_id}</Text>
              <Text style={styles.receiptDate}>{item.paid_date}</Text>
            </View>
            <View style={styles.receiptAmount}>
              <Text style={styles.amountLabel}>Amount</Text>
              <Text style={styles.amountValue}>
                {item.paid_amount !== null ? `₹${item.paid_amount.toLocaleString()}` : 'N/A'}
              </Text>
            </View>
          </View>
          
          <View style={styles.studentInfo}>
            <View style={styles.nameContainer}>
              <FontAwesome5 name="user-graduate" size={14} color="#7f8c8d" style={styles.nameIcon} />
              <Text style={styles.studentName}>{item.student_name}</Text>
            </View>
            <View style={styles.admissionContainer}>
              <FontAwesome5 name="id-card" size={14} color="#7f8c8d" style={styles.admissionIcon} />
              <Text style={styles.admissionNo}>{item.admission_no}</Text>
            </View>
          </View>
          
         
        </TouchableOpacity>
      </Animated.View>
    );
  };
  
  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#1e3c72" />
      
      <View style={styles.header}>
        <LinearGradient
          colors={['#1e3c72', '#2a5298']}
          style={styles.headerGradient}
        />
        <View style={styles.headerContent}>
          <TouchableOpacity 
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Collection Report</Text>
          <TouchableOpacity 
            style={styles.refreshButton}
            onPress={fetchReports}
          >
            <FontAwesome5 name="sync-alt" size={20} color="#ffffff" />
          </TouchableOpacity>
        </View>
      </View>
      
      <Animated.View 
        entering={FadeInDown.delay(100).springify()}
        style={styles.dateRangeContainer}
      >
        <View style={styles.datePickerRow}>
          <TouchableOpacity 
            style={styles.datePickerButton}
            onPress={() => setShowFromDatePicker(true)}
          >
            <Text style={styles.datePickerLabel}>From</Text>
            <View style={styles.dateDisplay}>
              <FontAwesome5 name="calendar-alt" size={14} color="#1e3c72" style={styles.calendarIcon} />
              <Text style={styles.dateText}>
                {fromDate.toISOString().split('T')[0]}
              </Text>
            </View>
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={styles.datePickerButton}
            onPress={() => setShowToDatePicker(true)}
          >
            <Text style={styles.datePickerLabel}>To</Text>
            <View style={styles.dateDisplay}>
              <FontAwesome5 name="calendar-alt" size={14} color="#1e3c72" style={styles.calendarIcon} />
              <Text style={styles.dateText}>
                {toDate.toISOString().split('T')[0]}
              </Text>
            </View>
          </TouchableOpacity>
        </View>
      </Animated.View>
      
      <Animated.View 
        entering={FadeInRight.delay(200).springify()}
        style={styles.summaryContainer}
      >
        <View style={styles.summaryCard}>
          <Text style={styles.summaryTitle}>Total Collection</Text>
          <Text style={styles.summaryValue}>₹{summaryData.totalAmount.toLocaleString()}</Text>
          <FontAwesome5 name="rupee-sign" size={18} color="rgba(76, 175, 80, 0.2)" style={styles.summaryIcon} />
        </View>
        
        <View style={styles.summaryCard}>
          <Text style={styles.summaryTitle}>Total Receipts</Text>
          <Text style={styles.summaryValue}>{summaryData.totalReceipts}</Text>
          <FontAwesome5 name="receipt" size={18} color="rgba(33, 150, 243, 0.2)" style={styles.summaryIcon} />
        </View>
        
        <View style={styles.summaryCard}>
          <Text style={styles.summaryTitle}>Avg. Amount</Text>
          <Text style={styles.summaryValue}>₹{summaryData.avgAmount.toFixed(0)}</Text>
          <FontAwesome5 name="chart-line" size={18} color="rgba(156, 39, 176, 0.2)" style={styles.summaryIcon} />
        </View>
      </Animated.View>
      
      {/* {filteredReports.length > 0 && (
        <Animated.View 
          entering={FadeInDown.delay(300).springify()}
          style={styles.chartContainer}
        >
          <Text style={styles.chartTitle}>Collection Trend</Text>
          <BarChart
            data={generateChartData()}
            width={width - 32}
            height={180}
            yAxisLabel="₹"
            chartConfig={{
              backgroundColor: '#ffffff',
              backgroundGradientFrom: '#ffffff',
              backgroundGradientTo: '#ffffff',
              decimalPlaces: 0,
              color: (opacity = 1) => `rgba(30, 60, 114, ${opacity})`,
              labelColor: (opacity = 1) => `rgba(0, 0, 0, ${opacity})`,
              style: {
                borderRadius: 16
              },
              barPercentage: 0.7
            }}
            style={styles.chart}
          />
        </Animated.View>
      )} */}
      
      <View style={styles.searchContainer}>
        <FontAwesome5 name="search" size={16} color="#95a5a6" style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Search by name or admission number"
          value={searchQuery}
          onChangeText={setSearchQuery}
          returnKeyType="search"
        />
        {searchQuery.length > 0 && (
          <TouchableOpacity onPress={() => setSearchQuery('')}>
            <FontAwesome5 name="times-circle" size={16} color="#95a5a6" />
          </TouchableOpacity>
        )}
      </View>
      
      {isLoading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#1e3c72" />
          <Text style={styles.loadingText}>Loading reports...</Text>
        </View>
      ) : filteredReports.length > 0 ? (
        <FlatList
          data={filteredReports}
          renderItem={renderReceiptItem}
          keyExtractor={item => item.receipt_id}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
        />
      ) : (
        <View style={styles.emptyContainer}>
          <FontAwesome5 name="file-invoice-dollar" size={60} color="#e0e0e0" />
          <Text style={styles.emptyTitle}>No reports found</Text>
          <Text style={styles.emptyText}>Try changing the date range or search criteria</Text>
        </View>
      )}
      
      {/* Date Pickers (Shown when active) */}
      {showFromDatePicker && (
        <DateTimePicker
          value={fromDate}
          mode="date"
          display="default"
          onChange={handleFromDateChange}
          maximumDate={toDate}
        />
      )}
      
      {showToDatePicker && (
        <DateTimePicker
          value={toDate}
          mode="date"
          display="default"
          onChange={handleToDateChange}
          minimumDate={fromDate}
          maximumDate={new Date()}
        />
      )}
      
      {/* Receipt Details Modal */}
      <Modal
        visible={showReceiptModal}
        transparent={true}
        animationType="slide"
        onRequestClose={() => setShowReceiptModal(false)}
      >
        <TouchableOpacity 
          style={styles.modalOverlay}
          activeOpacity={1}
          onPress={() => setShowReceiptModal(false)}
        >
          <View style={styles.modalContainer}>
            <View style={styles.modalContent}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Receipt Details</Text>
                <TouchableOpacity onPress={() => setShowReceiptModal(false)}>
                  <FontAwesome5 name="times" size={20} color="#7f8c8d" />
                </TouchableOpacity>
              </View>
              
              {selectedReceipt && (
                <View style={styles.receiptDetails}>
                  <View style={styles.receiptDetailRow}>
                    <Text style={styles.receiptDetailLabel}>Receipt Number</Text>
                    <Text style={styles.receiptDetailValue}>#{selectedReceipt.receipt_id}</Text>
                  </View>
                  
                  <View style={styles.receiptDetailRow}>
                    <Text style={styles.receiptDetailLabel}>Student Name</Text>
                    <Text style={styles.receiptDetailValue}>{selectedReceipt.student_name}</Text>
                  </View>
                  
                  <View style={styles.receiptDetailRow}>
                    <Text style={styles.receiptDetailLabel}>Admission No.</Text>
                    <Text style={styles.receiptDetailValue}>{selectedReceipt.admission_no}</Text>
                  </View>
                  
                  <View style={styles.receiptDetailRow}>
                    <Text style={styles.receiptDetailLabel}>Payment Date</Text>
                    <Text style={styles.receiptDetailValue}>{selectedReceipt.paid_date}</Text>
                  </View>
                  
                  <View style={styles.receiptDetailRow}>
                    <Text style={styles.receiptDetailLabel}>Amount Paid</Text>
                    <Text style={styles.receiptDetailValue}>
                      {selectedReceipt.paid_amount !== null 
                        ? `₹${selectedReceipt.paid_amount.toLocaleString()}` 
                        : 'N/A'}
                    </Text>
                  </View>
                  
                  <TouchableOpacity 
                    style={styles.viewFullButton}
                    onPress={viewFullReceipt}
                  >
                    <LinearGradient
                      colors={['#1e3c72', '#2a5298']}
                      style={styles.viewFullGradient}
                    >
                      <Text style={styles.viewFullText}>View Full Receipt</Text>
                      <FontAwesome5 name="external-link-alt" size={14} color="#ffffff" style={{ marginLeft: 6 }} />
                    </LinearGradient>
                  </TouchableOpacity>
                </View>
              )}
            </View>
          </View>
        </TouchableOpacity>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8f9fa',
  },
  header: {
    height: 120,
    overflow: 'hidden',
  },
  headerGradient: {
    position: 'absolute',
    left: 0,
    right: 0,
    top: 0,
    height: 120,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
    paddingTop: 20,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#ffffff',
  },
  refreshButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  dateRangeContainer: {
    backgroundColor: '#ffffff',
    marginHorizontal: 16,
    marginTop: -40,
    borderRadius: 12,
    padding: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  datePickerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  datePickerButton: {
    flex: 1,
    marginHorizontal: 4,
  },
  datePickerLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginBottom: 4,
  },
  dateDisplay: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5f6fa',
    borderWidth: 1,
    borderColor: '#ecf0f1',
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 8,
  },
  calendarIcon: {
    marginRight: 6,
  },
  dateText: {
    fontSize: 14,
    color: '#2c3e50',
  },
  summaryContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    marginTop: 16,
  },
  summaryCard: {
    width: (width - 48) / 3,
    backgroundColor: '#ffffff',
    borderRadius: 12,
    padding: 12,
    paddingBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 1,
    position: 'relative',
    overflow: 'hidden',
  },
  summaryTitle: {
    fontSize: 12,
    fontWeight: '500',
    color: '#7f8c8d',
    marginBottom: 6,
  },
  summaryValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  summaryIcon: {
    position: 'absolute',
    right: 6,
    bottom: 6,
    transform: [{ rotate: '-15deg' }],
  },
  chartContainer: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    margin: 16,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  chartTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 12,
  },
  chart: {
    borderRadius: 16,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    marginHorizontal: 16,
    marginBottom: 16,
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 10,
    marginTop:10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 1,
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    fontSize: 15,
    color: '#2c3e50',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 12,
    fontSize: 15,
    color: '#7f8c8d',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginTop: 16,
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 15,
    color: '#7f8c8d',
    textAlign: 'center',
  },
  listContainer: {
    padding: 16,
    paddingTop: 0,
  },
  receiptItem: {
    marginBottom: 16,
  },
  receiptCard: {
    backgroundColor: '#ffffff',
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 3,
    elevation: 2,
    padding: 16,
  },
  receiptHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f5f6fa',
    paddingBottom: 12,
  },
  receiptInfo: {
    flex: 1,
  },
  receiptId: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 4,
  },
  receiptDate: {
    fontSize: 13,
    color: '#7f8c8d',
  },
  receiptAmount: {
    alignItems: 'flex-end',
  },
  amountLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginBottom: 2,
  },
  amountValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  studentInfo: {
    marginBottom: 12,
  },
  nameContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 6,
  },
  nameIcon: {
    marginRight: 8,
  },
  studentName: {
    fontSize: 15,
    color: '#2c3e50',
    fontWeight: '500',
  },
  admissionContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  admissionIcon: {
    marginRight: 8,
  },
  admissionNo: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  viewButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-end',
  },
  viewButtonText: {
    fontSize: 14,
    color: '#3498db',
    marginRight: 6,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  modalContainer: {
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '70%',
  },
  modalContent: {
    padding: 20,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
    paddingBottom: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#ecf0f1',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  receiptDetails: {
    marginBottom: 20,
  },
  receiptDetailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#f5f6fa',
  },
  receiptDetailLabel: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  receiptDetailValue: {
    fontSize: 15,
    fontWeight: '500',
    color: '#2c3e50',
  },
  viewFullButton: {
    marginTop: 20,
    borderRadius: 12,
    overflow: 'hidden',
  },
  viewFullGradient: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 14,
  },
  viewFullText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  }
});