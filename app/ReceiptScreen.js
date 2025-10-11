import { FontAwesome5 } from '@expo/vector-icons';
import axios from 'axios';
import * as Haptics from 'expo-haptics';
import { LinearGradient } from 'expo-linear-gradient';
import * as Print from 'expo-print';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as Sharing from 'expo-sharing';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Image,
  SafeAreaView,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  TouchableOpacity,
  View
} from 'react-native';
//import QRCode from 'react-native-qrcode-svg';
import Animated, { FadeIn, SlideInDown } from 'react-native-reanimated';

export default function ReceiptScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const [isLoading, setIsLoading] = useState(true);
  const [receiptData, setReceiptData] = useState(null);
  const [error, setError] = useState(null);

  // Get receipt ID from params
  const receiptId = params.receipt_no || params.receipt_id || "";

  // School details - in a real app, these would come from your app config
  const schoolDetails = {
    name: "Delhi Public School, Mushkipur",
    address: "Mushkipur, Muzaffarpur, Bihar - 842002",
    phone: "+91 9876543210",
    email: "info@dpsmushkipur.com",
    website: "www.dpsmushkipur.com",
    logo: require("./assets/logo.png") // Update with your school logo path
  };

  useEffect(() => {
    fetchReceiptDetails();
  }, [receiptId]);

  const fetchReceiptDetails = async () => {
    setIsLoading(true);
    setError(null);
    
    try {
      if (!receiptId) {
        throw new Error("Receipt ID is required");
      }

      const response = await axios.post(
        'https://dpsmushkipur.com/bine/api.php?task=get_receipt',
        {
          receipt_id: receiptId
        }
      );
      
      if (response.data) {
        setReceiptData(response.data);
      } else {
        throw new Error("Invalid response from server");
      }
    } catch (err) {
      console.error("Error fetching receipt:", err);
      setError("Failed to load receipt details. " + (err.message || ""));
    } finally {
      setIsLoading(false);
    }
  };

  const generateReceiptHTML = () => {
    if (!receiptData) return '';

    // Format payment date
    const formattedDate = new Date(receiptData.payment_date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    // Determine paid amount (use total if paid_amount is null)
    const paidAmount = receiptData.paid_amount !== null ? receiptData.paid_amount : receiptData.total;
    
    // Split months if there are multiple
    const paidMonths = receiptData.paid_month.split(',').map(month => month.trim());
    
    // Create QR code data
    //const qrData = `Receipt:${receiptData.receipt_id},Student:${receiptData.admission_no},Amount:${paidAmount}`;

    return `
      <html>
      <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
          }
          .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #1e3c72;
            border-radius: 10px;
            overflow: hidden;
          }
          .header {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
          }
          .school-info {
            flex: 1;
          }
          .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
          }
          .school-details {
            font-size: 12px;
            margin-top: 5px;
          }
          .receipt-title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            color: #1e3c72;
          }
          .receipt-no {
            text-align: right;
            font-size: 14px;
            margin-bottom: 5px;
          }
          .content {
            padding: 20px;
          }
          .section {
            margin-bottom: 20px;
          }
          .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #1e3c72;
          }
          .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
          }
          .info-label {
            font-weight: bold;
            color: #666;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
          }
          table, th, td {
            border: 1px solid #ddd;
          }
          th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
          }
          td {
            padding: 8px;
          }
          .payment-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
          }
          .total-row {
            font-weight: bold;
            font-size: 16px;
            color: #1e3c72;
          }
          .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
            font-size: 12px;
            color: #666;
          }
          .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
          }
          .signature-box {
            text-align: center;
            width: 40%;
          }
          .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
          }
          .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(200, 200, 200, 0.1);
            z-index: -1;
          }
          .qr-container {
            text-align: center;
            margin-top: 20px;
          }
          .dues-notice {
            margin-top: 10px;
            padding: 10px;
            background-color: #fff3e0;
            border-radius: 5px;
            border-left: 3px solid #f39c12;
          }
        </style>
      </head>
      <body>
        <div class="receipt">
          <div class="header">
            <div class="school-info">
              <div class="school-name">${schoolDetails.name}</div>
              <div class="school-details">${schoolDetails.address}</div>
              <div class="school-details">Phone: ${schoolDetails.phone} | Email: ${schoolDetails.email}</div>
            </div>
          </div>
          
          <div class="content">
            <div class="receipt-title">FEE RECEIPT</div>
            <div class="receipt-no">Receipt No: ${receiptData.receipt_id}</div>
            <div class="receipt-no">Date: ${formattedDate}</div>
            
            <div class="section">
              <div class="section-title">Student Information</div>
              <div class="info-row">
                <span class="info-label">Name:</span>
                <span>${receiptData.student_name}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Admission No:</span>
                <span>${receiptData.admission_no}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Class:</span>
                <span>${receiptData.student_class}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Father's Name:</span>
                <span>${receiptData.father_name}</span>
              </div>
            </div>
            
            <div class="section">
              <div class="section-title">Fee Details</div>
              <table>
                <tr>
                  <th>Description</th>
                  <th>Amount (₹)</th>
                </tr>
                <tr>
                  <td>${paidMonths.join(', ')} - Tuition Fee</td>
                  <td>₹ ${parseFloat(receiptData.fee_details.tution_fee).toLocaleString()}</td>
                </tr>
                ${receiptData.fee_details.transport_fee ? `
                <tr>
                  <td>${paidMonths.join(', ')} - Transport Fee</td>
                  <td>₹ ${parseFloat(receiptData.fee_details.transport_fee).toLocaleString()}</td>
                </tr>
                ` : ''}
              </table>
              
              <div class="payment-summary">
                <div class="info-row">
                  <span class="info-label">Total Amount:</span>
                  <span>₹ ${parseFloat(receiptData.total).toLocaleString()}</span>
                </div>
                ${parseFloat(receiptData.discount) > 0 ? `
                <div class="info-row">
                  <span class="info-label">Discount:</span>
                  <span>₹ ${parseFloat(receiptData.discount).toLocaleString()}</span>
                </div>
                ` : ''}
                ${parseFloat(receiptData.misc_fee) > 0 ? `
                <div class="info-row">
                  <span class="info-label">Misc Fee:</span>
                  <span>₹ ${parseFloat(receiptData.misc_fee).toLocaleString()}</span>
                </div>
                ` : ''}
                <div class="info-row total-row">
                  <span class="info-label">Amount Paid:</span>
                  <span>₹ ${parseFloat(paidAmount).toLocaleString()}</span>
                </div>
                ${parseFloat(receiptData.current_dues) > 0 ? `
                <div class="dues-notice">
                  <div class="info-row">
                    <span class="info-label">Current Dues:</span>
                    <span>₹ ${parseFloat(receiptData.current_dues).toLocaleString()}</span>
                  </div>
                </div>
                ` : ''}
              </div>
              ${receiptData.remarks ? `
              <div style="margin-top: 15px;">
                <span class="info-label">Remarks:</span>
                <p>${receiptData.remarks}</p>
              </div>
              ` : ''}
            </div>
            
            <div class="signature">
              <div class="signature-box">
                <div class="signature-line"></div>
                <div>Parent's Signature</div>
              </div>
              
              <div class="signature-box">
                <div class="signature-line"></div>
                <div>Authorized Signatory</div>
                <div>DPS Mushkipur</div>
              </div>
            </div>
            
            <div class="qr-container">
              <!-- QR code would be inserted here in a real implementation -->
              <div>Scan to verify</div>
            </div>
            
            <div class="footer">
              <p>This is a computer-generated receipt and does not require a physical signature.</p>
              <p>For any queries, please contact the school office.</p>
            </div>
          </div>
        </div>
        
        <div class="watermark">PAID</div>
      </body>
      </html>
    `;
  };

  const printReceipt = async () => {
    try {
      Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
      
      const html = generateReceiptHTML();
      const { uri } = await Print.printToFileAsync({ html });
      
      // Check if sharing is available
      if (await Sharing.isAvailableAsync()) {
        await Sharing.shareAsync(uri, { UTI: '.pdf', mimeType: 'application/pdf' });
      } else {
        Alert.alert("Sharing not available", "Sharing is not available on this device.");
      }
    } catch (error) {
      console.error("Error printing receipt:", error);
      Alert.alert("Print Error", "There was an error while generating the receipt.");
    }
  };

  if (isLoading) {
    return (
      <SafeAreaView style={styles.loadingContainer}>
        <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
        <ActivityIndicator size="large" color="#1e3c72" />
        <Text style={styles.loadingText}>Loading receipt...</Text>
      </SafeAreaView>
    );
  }

  if (error) {
    return (
      <SafeAreaView style={styles.errorContainer}>
        <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
        <FontAwesome5 name="exclamation-triangle" size={50} color="#e74c3c" />
        <Text style={styles.errorTitle}>Error Loading Receipt</Text>
        <Text style={styles.errorMessage}>{error}</Text>
        <TouchableOpacity 
          style={styles.retryButton}
          onPress={fetchReceiptDetails}
        >
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </SafeAreaView>
    );
  }

  // Determine paid amount (use total if paid_amount is null)
  const paidAmount = receiptData.paid_amount !== null ? 
                     parseFloat(receiptData.paid_amount) : 
                     parseFloat(receiptData.total);

  // Determine if this is a partial payment
  const isPartialPayment = receiptData.paid_amount !== null && 
                          parseFloat(receiptData.paid_amount) < parseFloat(receiptData.total);

  // Split months if there are multiple
  const paidMonths = receiptData.paid_month.split(',').map(month => month.trim());

  // Check if there are current dues
  const hasDues = receiptData.current_dues && parseFloat(receiptData.current_dues) > 0;

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
      
      {/* Header with Watermark */}
      <View style={styles.watermark}>
        <Text style={styles.watermarkText}>PAID</Text>
      </View>
      
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => router.push('/Dashboard')}
        >
          <FontAwesome5 name="arrow-left" size={20} color="#1e3c72" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Fee Receipt</Text>
        <TouchableOpacity 
          style={styles.printButton}
          onPress={printReceipt}
        >
          <FontAwesome5 name="print" size={20} color="#1e3c72" />
        </TouchableOpacity>
      </View>
      
      <ScrollView
        style={styles.scrollContainer}
        contentContainerStyle={styles.contentContainer}
        showsVerticalScrollIndicator={false}
      >
        {/* Receipt Card */}
        <Animated.View 
          entering={FadeIn.delay(200).springify()}
          style={styles.receiptCard}
        >
          {/* School Info */}
          <LinearGradient
            colors={['#1e3c72', '#2a5298']}
            style={styles.schoolHeader}
          >
            <Image 
              source={schoolDetails.logo}
              style={styles.schoolLogo}
              defaultSource={require('./assets/default.png')} // Update with a fallback logo
            />
            <View style={styles.schoolInfo}>
              <Text style={styles.schoolName}>{schoolDetails.name}</Text>
              <Text style={styles.schoolAddress}>{schoolDetails.address}</Text>
            </View>
          </LinearGradient>
          
          {/* Receipt Title */}
          <View style={styles.receiptTitleContainer}>
            <Text style={styles.receiptTitle}>RECEIPT</Text>
            <View style={styles.receiptMeta}>
              <Text style={styles.receiptNo}>#{receiptData.receipt_id}</Text>
              <Text style={styles.receiptDate}>
                {new Date(receiptData.payment_date).toLocaleDateString()}
              </Text>
            </View>
          </View>
          
          {/* Student Information */}
          <Animated.View 
            entering={SlideInDown.delay(300).springify()}
            style={styles.section}
          >
            <View style={styles.sectionHeader}>
              <FontAwesome5 name="user-graduate" size={16} color="#1e3c72" />
              <Text style={styles.sectionTitle}>Student Information</Text>
            </View>
            
            <View style={styles.studentDetails}>
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Name</Text>
                <Text style={styles.detailValue}>{receiptData.student_name}</Text>
              </View>
              
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Admission No.</Text>
                <Text style={styles.detailValue}>{receiptData.admission_no}</Text>
              </View>
              
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Class</Text>
                <Text style={styles.detailValue}>{receiptData.student_class}</Text>
              </View>
              
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Father's Name</Text>
                <Text style={styles.detailValue}>{receiptData.father_name}</Text>
              </View>
            </View>
          </Animated.View>
          
          {/* Payment Details */}
          <Animated.View 
            entering={SlideInDown.delay(400).springify()}
            style={styles.section}
          >
            <View style={styles.sectionHeader}>
              <FontAwesome5 name="money-check-alt" size={16} color="#1e3c72" />
              <Text style={styles.sectionTitle}>Payment Details</Text>
            </View>
            
            <View style={styles.paymentDetails}>
              <View style={styles.paymentRow}>
                <View style={styles.paymentInfo}>
                  <FontAwesome5 
                    name="calendar-alt" 
                    size={16} 
                    color="#1e3c72" 
                    style={styles.paymentIcon}
                  />
                  <View>
                    <Text style={styles.paymentMethod}>
                      {paidMonths.length > 1 ? `${paidMonths.join(', ')} Fees` : `${paidMonths[0]} Fee`}
                    </Text>
                    <Text style={styles.paymentDate}>
                      Paid on {new Date(receiptData.payment_date).toLocaleDateString()}
                    </Text>
                  </View>
                </View>
                <View style={styles.amountContainer}>
                  <Text style={styles.amountLabel}>Paid Amount</Text>
                  <Text style={styles.amountValue}>₹{paidAmount.toLocaleString()}</Text>
                  
                  {isPartialPayment && (
                    <Text style={styles.partialPayment}>Partial Payment</Text>
                  )}
                </View>
              </View>
              
              <View style={styles.divider} />
              
              <View style={styles.feeBreakdown}>
                <Text style={styles.breakdownTitle}>Fee Breakdown</Text>
                
                {/* Only show fee types that are present in the response */}
                {receiptData.fee_details.tution_fee && (
                  <View style={styles.feeRow}>
                    <Text style={styles.feeLabel}>Tuition Fee</Text>
                    <Text style={styles.feeValue}>
                      ₹{parseFloat(receiptData.fee_details.tution_fee).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                {receiptData.fee_details.transport_fee && (
                  <View style={styles.feeRow}>
                    <Text style={styles.feeLabel}>Transport Fee</Text>
                    <Text style={styles.feeValue}>
                      ₹{parseFloat(receiptData.fee_details.transport_fee).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                {receiptData.fee_details.total && (
                  <View style={[styles.feeRow, styles.totalRow]}>
                    <Text style={styles.totalLabel}>Total</Text>
                    <Text style={styles.totalValue}>
                      ₹{parseFloat(receiptData.fee_details.total).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                {parseFloat(receiptData.discount) > 0 && (
                  <View style={styles.feeRow}>
                    <Text style={styles.feeLabel}>Discount</Text>
                    <Text style={styles.discountValue}>
                      - ₹{parseFloat(receiptData.discount).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                {parseFloat(receiptData.misc_fee) > 0 && (
                  <View style={styles.feeRow}>
                    <Text style={styles.feeLabel}>Misc. Fee</Text>
                    <Text style={styles.miscValue}>
                      + ₹{parseFloat(receiptData.misc_fee).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                <View style={[styles.feeRow, styles.netAmountRow]}>
                  <Text style={styles.netAmountLabel}>Net Amount</Text>
                  <Text style={styles.netAmountValue}>
                    ₹{parseFloat(receiptData.total).toLocaleString()}
                  </Text>
                </View>
                
                {/* Current Dues Section */}
                {hasDues && (
                  <View style={styles.duesContainer}>
                    <View style={styles.duesHeader}>
                      <FontAwesome5 name="exclamation-triangle" size={14} color="#f39c12" />
                      <Text style={styles.duesTitle}>Current Dues</Text>
                    </View>
                    <Text style={styles.duesAmount}>
                      ₹{parseFloat(receiptData.current_dues).toLocaleString()}
                    </Text>
                  </View>
                )}
                
                {receiptData.remarks && (
                  <View style={styles.remarksContainer}>
                    <Text style={styles.remarksLabel}>Remarks:</Text>
                    <Text style={styles.remarksText}>{receiptData.remarks}</Text>
                  </View>
                )}
              </View>
            </View>
          </Animated.View>
          
          {/* Verification */}
          <Animated.View 
            entering={SlideInDown.delay(500).springify()}
            style={[styles.section, styles.verificationSection]}
          >
            <View style={styles.qrContainer}>
              {/* <QRCode
                value={`Receipt:${receiptData.receipt_id},Student:${receiptData.admission_no},Amount:${paidAmount}`}
                size={90}
                color="#1e3c72"
                backgroundColor="white"
              /> */}
              <Text style={styles.scanText}>Scan to verify</Text>
            </View>
            
            <View style={styles.signatureContainer}>
              <View style={styles.signatureLine} />
              <Text style={styles.signatureName}>Authorized Signatory</Text>
              <Text style={styles.signaturePosition}>DPS Mushkipur</Text>
            </View>
          </Animated.View>
          
          {/* Footer */}
          <View style={styles.footer}>
            <Text style={styles.footerText}>
              This is a computer-generated receipt and doesn't require a signature.
            </Text>
            <Text style={styles.footerText}>
              For any queries, please contact the school office.
            </Text>
          </View>
        </Animated.View>
      </ScrollView>
      
      {/* Action Buttons */}
      <View style={styles.actionButtons}>
        <TouchableOpacity 
          style={[styles.actionButton, styles.shareButton]}
          onPress={printReceipt}
        >
          <LinearGradient
            colors={['#1e3c72', '#2a5298']}
            style={styles.buttonGradient}
          >
            <FontAwesome5 name="share-alt" size={18} color="#ffffff" />
            <Text style={styles.buttonText}>Share Receipt</Text>
          </LinearGradient>
        </TouchableOpacity>
        
        <TouchableOpacity 
          style={styles.actionButton}
          onPress={() => router.push('/dashboard')}
        >
          <Text style={styles.doneButtonText}>Done</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f6fa',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#ffffff',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#7f8c8d',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  errorTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginTop: 16,
    marginBottom: 8,
  },
  errorMessage: {
    fontSize: 16,
    color: '#7f8c8d',
    textAlign: 'center',
    marginBottom: 24,
  },
  retryButton: {
    backgroundColor: '#1e3c72',
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  watermark: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: -1,
  },
  watermarkText: {
    fontSize: 120,
    color: 'rgba(200, 200, 200, 0.2)',
    fontWeight: 'bold',
    transform: [{ rotate: '-45deg' }],
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f0f0f0',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  printButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f0f0f0',
  },
  scrollContainer: {
    flex: 1,
  },
  contentContainer: {
    padding: 16,
    paddingBottom: 100,
  },
  receiptCard: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 10,
    elevation: 5,
    overflow: 'hidden',
  },
  schoolHeader: {
    flexDirection: 'row',
    padding: 16,
    alignItems: 'center',
  },
  schoolLogo: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#ffffff',
    marginRight: 16,
  },
  schoolInfo: {
    flex: 1,
  },
  schoolName: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  schoolAddress: {
    color: '#ffffff',
    fontSize: 12,
    opacity: 0.8,
  },
  receiptTitleContainer: {
    alignItems: 'center',
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  receiptTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1e3c72',
    letterSpacing: 2,
  },
  receiptMeta: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 8,
  },
  receiptNo: {
    fontSize: 14,
    color: '#7f8c8d',
    marginRight: 16,
  },
  receiptDate: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  section: {
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginLeft: 8,
  },
  studentDetails: {
    backgroundColor: '#f8f9fa',
    padding: 16,
    borderRadius: 8,
  },
  detailRow: {
    flexDirection: 'row',
    marginBottom: 8,
  },
  detailLabel: {
    flex: 1,
    fontSize: 14,
    color: '#7f8c8d',
  },
  detailValue: {
    flex: 2,
    fontSize: 14,
    color: '#2c3e50',
    fontWeight: '500',
  },
  paymentDetails: {
    backgroundColor: '#f8f9fa',
    borderRadius: 8,
    overflow: 'hidden',
  },
  paymentRow: {
    flexDirection: 'row',
    padding: 16,
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  paymentInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  paymentIcon: {
    marginRight: 12,
  },
  paymentMethod: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  paymentDate: {
    fontSize: 12,
    color: '#7f8c8d',
    marginTop: 2,
  },
  amountContainer: {
    alignItems: 'flex-end',
  },
  amountLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginBottom: 2,
  },
  amountValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  partialPayment: {
    fontSize: 11,
    color: '#f39c12',
    marginTop: 4,
    backgroundColor: '#fff8e1',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
  },
  divider: {
    height: 1,
    backgroundColor: '#ecf0f1',
  },
  feeBreakdown: {
    padding: 16,
  },
  breakdownTitle: {
    fontSize: 15,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 12,
  },
  feeRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  feeLabel: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  feeValue: {
    fontSize: 14,
    color: '#2c3e50',
    fontWeight: '500',
  },
  totalRow: {
    borderTopWidth: 1,
    borderTopColor: '#ecf0f1',
    paddingTop: 8,
    marginTop: 4,
    marginBottom: 12,
  },
  totalLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  totalValue: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  discountValue: {
    fontSize: 14,
    color: '#e74c3c',
    fontWeight: '500',
  },
  miscValue: {
    fontSize: 14,
    color: '#3498db',
    fontWeight: '500',
  },
  netAmountRow: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#ecf0f1',
  },
  netAmountLabel: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  netAmountValue: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  duesContainer: {
    marginTop: 16,
    padding: 12,
    backgroundColor: '#fff8e1',
    borderRadius: 8,
    borderLeftWidth: 3,
    borderLeftColor: '#f39c12',
  },
  duesHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 6,
  },
  duesTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#f39c12',
    marginLeft: 6,
  },
  duesAmount: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#f39c12',
  },
  remarksContainer: {
    marginTop: 16,
    padding: 10,
    backgroundColor: '#f0f0f0',
    borderRadius: 6,
  },
  remarksLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 4,
  },
  remarksText: {
    fontSize: 13,
    color: '#7f8c8d',
  },
  verificationSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 0,
  },
  qrContainer: {
    alignItems: 'center',
  },
  scanText: {
    fontSize: 12,
    color: '#7f8c8d',
    marginTop: 8,
  },
  signatureContainer: {
    alignItems: 'center',
    marginTop: 16,
  },
  signatureLine: {
    width: 120,
    height: 1,
    backgroundColor: '#2c3e50',
    marginBottom: 8,
  },
  signatureName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  signaturePosition: {
    fontSize: 12,
    color: '#7f8c8d',
  },
  footer: {
    padding: 16,
    alignItems: 'center',
    backgroundColor: '#f8f9fa',
  },
  footerText: {
    fontSize: 12,
    color: '#95a5a6',
    textAlign: 'center',
    marginBottom: 4,
  },
  actionButtons: {
    flexDirection: 'row',
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 16,
    backgroundColor: '#ffffff',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 5,
  },
  actionButton: {
    flex: 1,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
    marginHorizontal: 8,
    borderRadius: 12,
    backgroundColor: '#f5f6fa',
  },
  shareButton: {
    overflow: 'hidden',
  },
  buttonGradient: {
    flex: 1,
    width: '100%',
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  buttonText: {
    color: '#ffffff',
    fontWeight: 'bold',
    fontSize: 16,
    marginLeft: 8,
  },
  doneButtonText: {
    color: '#1e3c72',
    fontWeight: 'bold',
    fontSize: 16,
  },
});