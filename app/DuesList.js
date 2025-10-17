import { FontAwesome5 } from "@expo/vector-icons";
import axios from "axios";
import * as FileSystem from "expo-file-system";
import { LinearGradient } from "expo-linear-gradient";
import * as Print from "expo-print";
import { useRouter } from "expo-router";
import * as Sharing from "expo-sharing";
import { useEffect, useState } from "react";
import {
  ActivityIndicator,
  Alert,
  FlatList,
  Linking,
  Modal,
  Platform,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";

import { SafeAreaProvider, SafeAreaView } from "react-native-safe-area-context";

// Class and Section options
const CLASS_OPTIONS = [
  { label: "NUR", value: "NUR" },
  { label: "LKG", value: "LKG" },
  { label: "UKG", value: "UKG" },
  { label: "I", value: "I" },
  { label: "II", value: "II" },
  { label: "III", value: "III" },
  { label: "IV", value: "IV" },
  { label: "V", value: "V" },
  { label: "VI", value: "VI" },
  { label: "VII", value: "VII" },
  { label: "VIII", value: "VIII" },
  { label: "IX", value: "IX" },
  { label: "X", value: "X" },
];

const SECTION_OPTIONS = [
  { label: "Section A", value: "A" },
  { label: "Section B", value: "B" },
  { label: "Section C", value: "C" },
];

const MONTH_OPTIONS = [
  { label: "January", value: "January" },
  { label: "February", value: "February" },
  { label: "March", value: "March" },
  { label: "April", value: "April" },
  { label: "May", value: "May" },
  { label: "June", value: "June" },
  { label: "July", value: "July" },
  { label: "August", value: "August" },
  { label: "September", value: "September" },
  { label: "October", value: "October" },
  { label: "November", value: "November" },
  { label: "December", value: "December" },
];

export default function DuesListScreen() {
  const router = useRouter();

  // Selection states
  const [selectedClass, setSelectedClass] = useState("I");
  const [selectedSection, setSelectedSection] = useState("A");
  const [selectedMonths, setSelectedMonths] = useState([
    "September",
    "October",
  ]);

  // Modal states
  const [showClassModal, setShowClassModal] = useState(false);
  const [showSectionModal, setShowSectionModal] = useState(false);
  const [showMonthsModal, setShowMonthsModal] = useState(false);

  // Data and loading states
  const [duesData, setDuesData] = useState([]);
  const [filteredData, setFilteredData] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");

  // Summary data
  const [summary, setSummary] = useState({
    totalStudents: 0,
    totalDues: 0,
    averageDue: 0,
  });

  // Toggle for filter section visibility
  const [showFilters, setShowFilters] = useState(false);

  // Effects
  useEffect(() => {
    if (selectedClass && selectedSection && selectedMonths.length > 0) {
      fetchDuesData();
    }
  }, [selectedClass, selectedSection, selectedMonths]);

  useEffect(() => {
    if (duesData.length > 0) {
      filterData();
      calculateSummary();
    }
  }, [duesData, searchQuery]);

  const fetchDuesData = async () => {
    setIsLoading(true);
    setError(null);

    try {
      const response = await axios.post(
        "https://dpsmushkipur.com/bine/api.php?task=dues_list",
        {
          student_class: selectedClass,
          student_section: selectedSection,
          months: selectedMonths,
        }
      );

      if (Array.isArray(response.data)) {
        setDuesData(response.data);
        setFilteredData(response.data);
      } else {
        throw new Error("Invalid response format from server");
      }
    } catch (err) {
      console.error("Error fetching dues data:", err);
      setError("Failed to load dues data. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  const filterData = () => {
    if (!searchQuery.trim()) {
      setFilteredData(duesData);
      return;
    }

    const filtered = duesData.filter(
      (student) =>
        student.student_name
          .toLowerCase()
          .includes(searchQuery.toLowerCase()) ||
        student.student_admission.includes(searchQuery) ||
        student.student_mobile.includes(searchQuery)
    );

    setFilteredData(filtered);
  };

  const calculateSummary = () => {
    const totalStudents = duesData.length;
    let totalDues = 0;

    duesData.forEach((student) => {
      const previousDues = parseFloat(student.previous_dues) || 0;
      const currentFees = student.fee?.total || 0;
      totalDues += previousDues + currentFees;
    });

    const averageDue =
      totalStudents > 0 ? Math.round(totalDues / totalStudents) : 0;

    setSummary({
      totalStudents,
      totalDues,
      averageDue,
    });
  };

  const toggleMonthSelection = (month) => {
    if (selectedMonths.includes(month)) {
      // Don't allow deselecting if only one month is selected
      if (selectedMonths.length > 1) {
        setSelectedMonths(selectedMonths.filter((m) => m !== month));
      }
    } else {
      setSelectedMonths([...selectedMonths, month]);
    }
  };

  const formatCurrency = (amount) => {
    return "₹" + parseFloat(amount).toLocaleString("en-IN");
  };

  const generatePDF = async () => {
    try {
      const htmlContent = `
        <html>
          <head>
            <style>
              body {
                font-family: Arial, sans-serif;
                margin: 20px;
              }
              h1 {
                color: #1e3c72;
                text-align: center;
                font-size: 24px;
              }
              .header-details {
                text-align: center;
                margin-bottom: 20px;
                font-size: 14px;
                color: #555;
              }
              table {
                width: 100%;
                border-collapse: collapse;
              }
              th {
                background-color: #1e3c72;
                color: white;
                padding: 10px;
                text-align: left;
                font-size: 12px;
              }
              td {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                font-size: 12px;
              }
              tr:nth-child(even) {
                background-color: #f9f9f9;
              }
              .summary {
                margin-top: 20px;
                padding: 15px;
                background-color: #f1f5f9;
                border-radius: 5px;
              }
              .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 12px;
                color: #777;
              }
            </style>
          </head>
          <body>
            <h1>Dues List Report</h1>
            <div class="header-details">
              Class: ${selectedClass}-${selectedSection} | Months: ${selectedMonths.join(
        ", "
      )}
              <br>Date: ${new Date().toLocaleDateString()}
            </div>
            
            <table>
              <tr>
                <th>Student Name</th>
                <th>Admission No.</th>
                <th>Previous Dues</th>
                <th>Current Fees</th>
                <th>Total Due</th>
              </tr>
              ${filteredData
                .map(
                  (student) => `
                <tr>
                  <td>${student.student_name}</td>
                  <td>${student.student_admission}</td>
                  <td>${formatCurrency(student.previous_dues)}</td>
                  <td>${formatCurrency(student.fee.total)}</td>
                  <td>${formatCurrency(
                    parseFloat(student.previous_dues) + student.fee.total
                  )}</td>
                </tr>
              `
                )
                .join("")}
            </table>
            
            <div class="summary">
              <strong>Summary:</strong>
              <br>Total Students with Dues: ${summary.totalStudents}
              <br>Total Outstanding Amount: ${formatCurrency(summary.totalDues)}
              <br>Average Due per Student: ${formatCurrency(summary.averageDue)}
            </div>
            
            <div class="footer">
              DPS Mushkipur - Dues Report Generated on ${new Date().toLocaleString()}
            </div>
          </body>
        </html>
      `;

      const { uri } = await Print.printToFileAsync({ html: htmlContent });

      // Check if sharing is available
      if (await Sharing.isAvailableAsync()) {
        await Sharing.shareAsync(uri, {
          UTI: ".pdf",
          mimeType: "application/pdf",
          dialogTitle: "Share Dues List PDF Report",
        });
      } else {
        Alert.alert(
          "Sharing not available",
          "Sharing is not available on this device"
        );
      }
    } catch (error) {
      console.error("Error generating PDF:", error);
      Alert.alert("Error", "Failed to generate PDF report");
    }
  };

  const generateExcel = async () => {
    try {
      // Create CSV content
      let csvContent =
        "Student Name,Admission No,Mobile,Previous Dues,Tuition Fee,Transport Fee,Total Fee,Grand Total\n";

      filteredData.forEach((student) => {
        const previousDues = parseFloat(student.previous_dues) || 0;
        const tuitionFee = student.fee?.tution_fee || 0;
        const transportFee = student.fee?.transport_fee || 0;
        const totalFee = student.fee?.total || 0;
        const grandTotal = previousDues + totalFee;

        csvContent += `"${student.student_name}",${student.student_admission},${student.student_mobile},${previousDues},${tuitionFee},${transportFee},${totalFee},${grandTotal}\n`;
      });

      // Add summary
      csvContent += "\nSummary\n";
      csvContent += `Total Students,${summary.totalStudents}\n`;
      csvContent += `Total Dues,${summary.totalDues}\n`;
      csvContent += `Average Due,${summary.averageDue}\n`;

      // Generate file name with timestamp for uniqueness
      const timestamp = new Date().getTime();
      const fileName = `DuesList_${selectedClass}_${selectedSection}_${timestamp}.csv`;

      // Write to file in app's temporary directory
      const fileUri = `${FileSystem.cacheDirectory}${fileName}`;

      await FileSystem.writeAsStringAsync(fileUri, csvContent, {
        encoding: FileSystem.EncodingType.UTF8,
      });

      // Share the file
      if (await Sharing.isAvailableAsync()) {
        await Sharing.shareAsync(fileUri, {
          mimeType: "text/csv",
          dialogTitle: `Dues List - ${selectedClass}${selectedSection}`,
        });
      } else {
        Alert.alert(
          "Sharing not available",
          "Sharing is not available on this device"
        );
      }
    } catch (error) {
      console.error("Error generating Excel/CSV:", error);
      Alert.alert("Error", "Failed to generate Excel report: " + error.message);
    }
  };

  // Function to make a phone call

 const makePhoneCall = async (phoneNumber) => {
  // Trim and validate phone number
  const cleanedNumber = String(phoneNumber).trim();

  if (!cleanedNumber || !/^\+?\d{7,15}$/.test(cleanedNumber)) {
    Alert.alert("Invalid Number", "Please provide a valid phone number.");
    return;
  }

  // Choose URL scheme based on platform
  const phoneUrl =
    Platform.OS === "android"
      ? `tel:${cleanedNumber}`
      : `telprompt:${cleanedNumber}`;

  try {
    const supported = await Linking.canOpenURL(phoneUrl);
    if (supported) {
      await Linking.openURL(phoneUrl);
    } else {
      Alert.alert("Not Supported", "Your device cannot make this call.");
    }
  } catch (error) {
    console.error("Error making phone call:", error);
    Alert.alert("Error", "Something went wrong while trying to make the call.");
  }
};

  // Function to send WhatsApp message
  const sendWhatsAppMessage = (phoneNumber, studentName, amount) => {
    if (!phoneNumber) return;

    // Remove any non-numeric characters from phone number
    const cleanedNumber = phoneNumber.replace(/\D/g, "");

    // Add country code if not present (assuming India +91)
    const formattedNumber =
      cleanedNumber.length === 10 ? `91${cleanedNumber}` : cleanedNumber;

    // Create a message with student details and due amount
    const message = `Dear Parent, This is to remind you that fee payment of ${formatCurrency(
      amount
    )} is pending for ${studentName}. Kindly pay at your earliest convenience. Thank you, DPS Mushkipur`;

    // Encode the message for URL
    const encodedMessage = encodeURIComponent(message);

    // Create WhatsApp URL
    const whatsappUrl = `whatsapp://send?phone=${formattedNumber}&text=${encodedMessage}`;

    Linking.canOpenURL(whatsappUrl)
      .then((supported) => {
        if (supported) {
          return Linking.openURL(whatsappUrl);
        }
        Alert.alert("WhatsApp is not installed on this device");
      })
      .catch((err) => console.error("Error opening WhatsApp:", err));
  };

  const renderDuesItem = ({ item }) => {
    const previousDues = parseFloat(item.previous_dues) || 0;
    const currentFees = item.fee?.total || 0;
    const totalDue = previousDues + currentFees;

    return (
      <View style={styles.duesCard}>
        <View style={styles.cardHeader}>
          <Text style={styles.studentName}>{item.student_name}</Text>
          <View style={styles.amountContainer}>
            <Text style={styles.amountLabel}>Total Due</Text>
            <Text style={styles.totalAmount}>{formatCurrency(totalDue)}</Text>
          </View>
        </View>

        <View style={styles.cardDetails}>
          <View style={styles.detailRow}>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Admission No.</Text>
              <Text style={styles.detailValue}>{item.student_admission}</Text>
            </View>

            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Mobile</Text>
              <Text style={styles.detailValue}>{item.student_mobile}</Text>
            </View>
          </View>

          <View style={styles.divider} />

          <View style={styles.feesSection}>
            <View style={styles.feeRow}>
              <Text style={styles.feeLabel}>Previous Dues</Text>
              <Text style={[styles.feeValue, styles.previousDue]}>
                {formatCurrency(previousDues)}
              </Text>
            </View>

            {item.fee?.tution_fee && (
              <View style={styles.feeRow}>
                <Text style={styles.feeLabel}>Tuition Fee</Text>
                <Text style={styles.feeValue}>
                  {formatCurrency(item.fee.tution_fee)}
                </Text>
              </View>
            )}

            {item.fee?.transport_fee && (
              <View style={styles.feeRow}>
                <Text style={styles.feeLabel}>Transport Fee</Text>
                <Text style={styles.feeValue}>
                  {formatCurrency(item.fee.transport_fee)}
                </Text>
              </View>
            )}

            <View style={[styles.feeRow, styles.currentFeeRow]}>
              <Text style={styles.currentFeeLabel}>Current Fee</Text>
              <Text style={styles.currentFeeValue}>
                {formatCurrency(currentFees)}
              </Text>
            </View>
          </View>
        </View>

        <View style={styles.cardFooter}>
          <TouchableOpacity
            style={styles.callButton}
            onPress={() => makePhoneCall(item.student_mobile)}
          >
            <FontAwesome5 name="phone-alt" size={14} color="#3498db" />
            <Text style={styles.callButtonText}>Call</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.messageButton}
            onPress={() =>
              sendWhatsAppMessage(
                item.student_mobile,
                item.student_name,
                totalDue
              )
            }
          >
            <FontAwesome5 name="whatsapp" size={14} color="#25D366" />
            <Text style={styles.messageButtonText}>WhatsApp</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.viewButton}
            onPress={() =>
              router.push({
                pathname: "/student-profile",
                params: { student_id: item.id },
              })
            }
          >
            <FontAwesome5 name="user" size={14} color="#1e3c72" />
            <Text style={styles.viewButtonText}>Profile</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  };

  return (
    <SafeAreaProvider>
      <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
        <StatusBar barStyle="light-content" backgroundColor="#1e3c72" />

        {/* Header */}
        <View style={styles.header}>
          <LinearGradient
            colors={["#1e3c72", "#2a5298"]}
            style={styles.headerGradient}
          >
            <View style={styles.headerContent}>
              <TouchableOpacity
                style={styles.backButton}
                onPress={() => router.back()}
              >
                <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
              </TouchableOpacity>

              <Text style={styles.headerTitle}>Dues List</Text>

              <View style={styles.actionButtons}>
                <TouchableOpacity
                  style={styles.headerButton}
                  onPress={generatePDF}
                >
                  <FontAwesome5 name="file-pdf" size={18} color="#ffffff" />
                </TouchableOpacity>

                {/* <TouchableOpacity
                  style={styles.headerButton}
                  onPress={generateExcel}
                >
                  <FontAwesome5 name="file-excel" size={18} color="#ffffff" />
                </TouchableOpacity> */}
              </View>
            </View>
          </LinearGradient>
        </View>

        {/* Collapsible Filter Bar */}
        <View style={styles.filterToggleContainer}>
          <View style={styles.selectedInfoContainer}>
            <Text style={styles.selectedInfoText}>
              Class {selectedClass}-{selectedSection} • {selectedMonths.length}{" "}
              months
            </Text>
          </View>

          <TouchableOpacity
            style={styles.filterToggleButton}
            onPress={() => setShowFilters(!showFilters)}
          >
            <FontAwesome5
              name={showFilters ? "chevron-up" : "chevron-down"}
              size={14}
              color="#7f8c8d"
            />
          </TouchableOpacity>
        </View>

        {/* Expandable Filters Section */}
        {showFilters && (
          <>
            {/* Selection Bar */}
            <View style={styles.selectionBar}>
              <View style={styles.selectionRow}>
                {/* Class Selector */}
                <TouchableOpacity
                  style={styles.selector}
                  onPress={() => setShowClassModal(true)}
                >
                  <Text style={styles.selectorLabel}>Class</Text>
                  <View style={styles.selectorValue}>
                    <Text style={styles.selectedValueText}>
                      {selectedClass}
                    </Text>
                    <FontAwesome5
                      name="chevron-down"
                      size={12}
                      color="#7f8c8d"
                    />
                  </View>
                </TouchableOpacity>

                {/* Section Selector */}
                <TouchableOpacity
                  style={styles.selector}
                  onPress={() => setShowSectionModal(true)}
                >
                  <Text style={styles.selectorLabel}>Section</Text>
                  <View style={styles.selectorValue}>
                    <Text style={styles.selectedValueText}>
                      {selectedSection}
                    </Text>
                    <FontAwesome5
                      name="chevron-down"
                      size={12}
                      color="#7f8c8d"
                    />
                  </View>
                </TouchableOpacity>
              </View>

              {/* Month Selector */}
              <TouchableOpacity
                style={styles.monthSelector}
                onPress={() => setShowMonthsModal(true)}
              >
                <Text style={styles.monthSelectorLabel}>Selected Months</Text>
                <View style={styles.selectedMonthsContainer}>
                  {selectedMonths.map((month) => (
                    <View key={month} style={styles.monthChip}>
                      <Text style={styles.monthChipText}>{month}</Text>
                    </View>
                  ))}
                  <FontAwesome5
                    name="edit"
                    size={12}
                    color="#7f8c8d"
                    style={styles.editIcon}
                  />
                </View>
              </TouchableOpacity>
            </View>

            {/* Summary Cards */}
            <View style={styles.summaryContainer}>
              <View style={styles.summaryCard}>
                <Text style={styles.summaryValue}>{summary.totalStudents}</Text>
                <Text style={styles.summaryLabel}>Students</Text>
              </View>

              <View style={styles.summaryCard}>
                <Text style={styles.summaryValue}>
                  {formatCurrency(summary.totalDues)}
                </Text>
                <Text style={styles.summaryLabel}>Total Dues</Text>
              </View>

              <View style={styles.summaryCard}>
                <Text style={styles.summaryValue}>
                  {formatCurrency(summary.averageDue)}
                </Text>
                <Text style={styles.summaryLabel}>Avg. Due</Text>
              </View>
            </View>
          </>
        )}

        {/* Search Box */}
        <View style={styles.searchContainer}>
          <FontAwesome5 name="search" size={16} color="#95a5a6" />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by name or admission number..."
            placeholderTextColor="#95a5a6"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
          {searchQuery.length > 0 && (
            <TouchableOpacity onPress={() => setSearchQuery("")}>
              <FontAwesome5 name="times" size={16} color="#95a5a6" />
            </TouchableOpacity>
          )}
        </View>

        {/* Dues List */}
        {isLoading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color="#1e3c72" />
            <Text style={styles.loadingText}>Loading dues data...</Text>
          </View>
        ) : error ? (
          <View style={styles.errorContainer}>
            <FontAwesome5 name="exclamation-circle" size={50} color="#e74c3c" />
            <Text style={styles.errorTitle}>Error Loading Data</Text>
            <Text style={styles.errorMessage}>{error}</Text>
            <TouchableOpacity
              style={styles.retryButton}
              onPress={fetchDuesData}
            >
              <Text style={styles.retryText}>Retry</Text>
            </TouchableOpacity>
          </View>
        ) : filteredData.length > 0 ? (
          <FlatList
            data={filteredData}
            renderItem={renderDuesItem}
            keyExtractor={(item) => item.id}
            contentContainerStyle={styles.listContainer}
            showsVerticalScrollIndicator={true}
          />
        ) : (
          <View style={styles.emptyContainer}>
            <FontAwesome5
              name="file-invoice-dollar"
              size={50}
              color="#e0e0e0"
            />
            <Text style={styles.emptyTitle}>No Dues Found</Text>
            <Text style={styles.emptyText}>
              No students with outstanding dues for the selected criteria
            </Text>
          </View>
        )}

        {/* Class Selection Modal */}
        <Modal
          visible={showClassModal}
          transparent={true}
          animationType="fade"
          onRequestClose={() => setShowClassModal(false)}
        >
          <View style={styles.modalOverlay}>
            <View style={styles.modalContainer}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Select Class</Text>
                <TouchableOpacity onPress={() => setShowClassModal(false)}>
                  <FontAwesome5 name="times" size={20} color="#7f8c8d" />
                </TouchableOpacity>
              </View>

              <View style={styles.modalContent}>
                {CLASS_OPTIONS.map((option) => (
                  <TouchableOpacity
                    key={option.value}
                    style={[
                      styles.modalOption,
                      selectedClass === option.value &&
                        styles.modalOptionSelected,
                    ]}
                    onPress={() => {
                      setSelectedClass(option.value);
                      setShowClassModal(false);
                    }}
                  >
                    <Text
                      style={[
                        styles.modalOptionText,
                        selectedClass === option.value &&
                          styles.modalOptionTextSelected,
                      ]}
                    >
                      {option.label}
                    </Text>
                    {selectedClass === option.value && (
                      <FontAwesome5 name="check" size={16} color="#2ecc71" />
                    )}
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </View>
        </Modal>

        {/* Section Selection Modal */}
        <Modal
          visible={showSectionModal}
          transparent={true}
          animationType="fade"
          onRequestClose={() => setShowSectionModal(false)}
        >
          <View style={styles.modalOverlay}>
            <View style={styles.modalContainer}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Select Section</Text>
                <TouchableOpacity onPress={() => setShowSectionModal(false)}>
                  <FontAwesome5 name="times" size={20} color="#7f8c8d" />
                </TouchableOpacity>
              </View>

              <View style={styles.modalContent}>
                {SECTION_OPTIONS.map((option) => (
                  <TouchableOpacity
                    key={option.value}
                    style={[
                      styles.modalOption,
                      selectedSection === option.value &&
                        styles.modalOptionSelected,
                    ]}
                    onPress={() => {
                      setSelectedSection(option.value);
                      setShowSectionModal(false);
                    }}
                  >
                    <Text
                      style={[
                        styles.modalOptionText,
                        selectedSection === option.value &&
                          styles.modalOptionTextSelected,
                      ]}
                    >
                      {option.label}
                    </Text>
                    {selectedSection === option.value && (
                      <FontAwesome5 name="check" size={16} color="#2ecc71" />
                    )}
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </View>
        </Modal>

        {/* Month Selection Modal */}
        <Modal
          visible={showMonthsModal}
          transparent={true}
          animationType="fade"
          onRequestClose={() => setShowMonthsModal(false)}
        >
          <View style={styles.modalOverlay}>
            <View style={styles.modalContainer}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Select Months</Text>
                <TouchableOpacity onPress={() => setShowMonthsModal(false)}>
                  <FontAwesome5 name="times" size={20} color="#7f8c8d" />
                </TouchableOpacity>
              </View>

              <ScrollView style={styles.modalContent}>
                {MONTH_OPTIONS.map((option) => (
                  <TouchableOpacity
                    key={option.value}
                    style={[
                      styles.modalOption,
                      selectedMonths.includes(option.value) &&
                        styles.modalOptionSelected,
                    ]}
                    onPress={() => toggleMonthSelection(option.value)}
                  >
                    <Text
                      style={[
                        styles.modalOptionText,
                        selectedMonths.includes(option.value) &&
                          styles.modalOptionTextSelected,
                      ]}
                    >
                      {option.label}
                    </Text>
                    {selectedMonths.includes(option.value) && (
                      <FontAwesome5 name="check" size={16} color="#2ecc71" />
                    )}
                  </TouchableOpacity>
                ))}
              </ScrollView>

              <View style={styles.modalFooter}>
                <TouchableOpacity
                  style={styles.modalDoneButton}
                  onPress={() => setShowMonthsModal(false)}
                >
                  <Text style={styles.modalDoneText}>Done</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </Modal>
      </SafeAreaView>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f6fa",
  },
  header: {
    height: 65,
    width: "100%",
  },
  headerGradient: {
    flex: 1,
  },
  headerContent: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingVertical: 16,
  },
  backButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    justifyContent: "center",
    alignItems: "center",
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#ffffff",
  },
  actionButtons: {
    flexDirection: "row",
  },
  headerButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 8,
  },
  // Filter toggle styles
  filterToggleContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#f0f0f0",
  },
  selectedInfoContainer: {
    flex: 1,
  },
  selectedInfoText: {
    fontSize: 14,
    color: "#2c3e50",
    fontWeight: "500",
  },
  filterToggleButton: {
    width: 30,
    height: 30,
    borderRadius: 15,
    backgroundColor: "#f8f9fa",
    justifyContent: "center",
    alignItems: "center",
  },
  // Selection styles
  selectionBar: {
    backgroundColor: "#ffffff",
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#f0f0f0",
  },
  selectionRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 12,
  },
  selector: {
    flex: 1,
    marginHorizontal: 4,
  },
  selectorLabel: {
    fontSize: 12,
    color: "#7f8c8d",
    marginBottom: 4,
  },
  selectorValue: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#f5f6fa",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderWidth: 1,
    borderColor: "#ecf0f1",
  },
  selectedValueText: {
    fontSize: 14,
    fontWeight: "500",
    color: "#2c3e50",
  },
  monthSelector: {
    marginTop: 4,
  },
  monthSelectorLabel: {
    fontSize: 12,
    color: "#7f8c8d",
    marginBottom: 4,
  },
  selectedMonthsContainer: {
    flexDirection: "row",
    alignItems: "center",
    flexWrap: "wrap",
    backgroundColor: "#f5f6fa",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderWidth: 1,
    borderColor: "#ecf0f1",
  },
  monthChip: {
    backgroundColor: "#1e3c72",
    borderRadius: 16,
    paddingHorizontal: 10,
    paddingVertical: 4,
    marginRight: 8,
    marginBottom: 4,
  },
  monthChipText: {
    color: "#ffffff",
    fontSize: 12,
    fontWeight: "500",
  },
  editIcon: {
    marginLeft: "auto",
  },
  searchContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    margin: 16,
    marginBottom: 12,
    borderRadius: 10,
    paddingHorizontal: 16,
    paddingVertical: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 1,
  },
  searchInput: {
    flex: 1,
    marginLeft: 8,
    fontSize: 14,
    color: "#2c3e50",
  },
  summaryContainer: {
    flexDirection: "row",
    paddingHorizontal: 8,
    marginBottom: 16,
  },
  summaryCard: {
    flex: 1,
    backgroundColor: "#ffffff",
    borderRadius: 12,
    padding: 16,
    margin: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 1,
    alignItems: "center",
  },
  summaryValue: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#1e3c72",
    marginBottom: 4,
  },
  summaryLabel: {
    fontSize: 12,
    color: "#7f8c8d",
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: "#7f8c8d",
  },
  errorContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  errorTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#2c3e50",
    marginTop: 16,
    marginBottom: 8,
  },
  errorMessage: {
    fontSize: 14,
    color: "#7f8c8d",
    textAlign: "center",
    marginBottom: 24,
  },
  retryButton: {
    backgroundColor: "#1e3c72",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 8,
  },
  retryText: {
    color: "#ffffff",
    fontWeight: "bold",
    fontSize: 14,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#2c3e50",
    marginTop: 16,
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 14,
    color: "#7f8c8d",
    textAlign: "center",
  },
  listContainer: {
    padding: 16,
    paddingTop: 0,
  },
  duesCard: {
    backgroundColor: "#ffffff",
    borderRadius: 12,
    marginBottom: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
    overflow: "hidden",
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#f0f0f0",
  },
  studentName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#2c3e50",
  },
  amountContainer: {
    alignItems: "flex-end",
  },
  amountLabel: {
    fontSize: 10,
    color: "#7f8c8d",
    marginBottom: 2,
  },
  totalAmount: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#e74c3c",
  },
  cardDetails: {
    padding: 16,
  },
  detailRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  detailItem: {
    flex: 1,
  },
  detailLabel: {
    fontSize: 12,
    color: "#7f8c8d",
    marginBottom: 2,
  },
  detailValue: {
    fontSize: 14,
    color: "#2c3e50",
  },
  divider: {
    height: 1,
    backgroundColor: "#f0f0f0",
    marginVertical: 12,
  },
  feesSection: {
    marginTop: 4,
  },
  feeRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 8,
  },
  feeLabel: {
    fontSize: 14,
    color: "#7f8c8d",
  },
  feeValue: {
    fontSize: 14,
    color: "#2c3e50",
  },
  previousDue: {
    color: "#e74c3c",
  },
  currentFeeRow: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: "#f0f0f0",
  },
  currentFeeLabel: {
    fontSize: 14,
    fontWeight: "bold",
    color: "#2c3e50",
  },
  currentFeeValue: {
    fontSize: 14,
    fontWeight: "bold",
    color: "#2c3e50",
  },
  cardFooter: {
    flexDirection: "row",
    borderTopWidth: 1,
    borderTopColor: "#f0f0f0",
  },
  callButton: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
    borderRightWidth: 1,
    borderRightColor: "#f0f0f0",
  },
  callButtonText: {
    fontSize: 14,
    color: "#3498db",
    marginLeft: 6,
  },
  messageButton: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
    borderRightWidth: 1,
    borderRightColor: "#f0f0f0",
  },
  messageButtonText: {
    fontSize: 14,
    color: "#25D366",
    marginLeft: 6,
  },
  viewButton: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
  },
  viewButtonText: {
    fontSize: 14,
    color: "#1e3c72",
    marginLeft: 6,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  modalContainer: {
    width: "85%",
    maxHeight: "70%",
    backgroundColor: "#ffffff",
    borderRadius: 12,
    overflow: "hidden",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 5,
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#ecf0f1",
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#2c3e50",
  },
  modalContent: {
    maxHeight: "80%",
  },
  modalOption: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 16,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    borderBottomColor: "#f5f6fa",
  },
  modalOptionSelected: {
    backgroundColor: "#f8f9fa",
  },
  modalOptionText: {
    fontSize: 16,
    color: "#2c3e50",
  },
  modalOptionTextSelected: {
    fontWeight: "bold",
    color: "#2c3e50",
  },
  modalFooter: {
    borderTopWidth: 1,
    borderTopColor: "#ecf0f1",
    padding: 12,
    alignItems: "flex-end",
  },
  modalDoneButton: {
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    backgroundColor: "#1e3c72",
  },
  modalDoneText: {
    color: "#ffffff",
    fontWeight: "bold",
    fontSize: 14,
  },
});
