import { FontAwesome5 } from "@expo/vector-icons";
import { LinearGradient } from "expo-linear-gradient";
import { useLocalSearchParams, useRouter } from "expo-router";
import { useRef, useState } from "react";
import {
  ActivityIndicator,
  Linking,
  SafeAreaView,
  StatusBar,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import { WebView } from "react-native-webview";

export default function PaymentWebViewScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const webViewRef = useRef(null);

  const { student_id, student_name, amount, selected_months, transaction_id } = params;

  const [isLoading, setIsLoading] = useState(true);
  const [canGoBack, setCanGoBack] = useState(false);

  const selectedMonthsArr = JSON.parse(selected_months || "[]");
  const paymentUrl =
    `https://dpsmushkipur.com/bine/ppay/index.php` +
    `?student_id=${encodeURIComponent(student_id)}` +
    `&amount=${encodeURIComponent(amount)}` +
    `&name=${encodeURIComponent(student_name || "")}` +
    `&selected_months=${encodeURIComponent(JSON.stringify(selectedMonthsArr))}`;

  const handleNavigationChange = (navState) => {
    setCanGoBack(navState.canGoBack);
    const url = navState.url || "";

    if (url.includes("payment_success.php")) {
      const urlParams = new URL(url);
      const txn = urlParams.searchParams.get("txn") || transaction_id;
      router.replace({
        pathname: "/PaymentStatusScreen",
        params: { transaction_id: txn, student_id, student_name, amount, status: "success" },
      });
      return;
    }

    if (url.includes("payment_failed.php")) {
      router.replace({
        pathname: "/PaymentStatusScreen",
        params: { transaction_id, student_id, student_name, amount, status: "failed" },
      });
    }
  };

  const handleShouldStartLoad = (event) => {
    const url = event.url || "";

    // 1. Allow normal web pages
    if (url.startsWith("http://") || url.startsWith("https://")) {
      return true;
    }

    // 2. Handle Android intent:// URLs
    if (url.startsWith("intent://")) {
      try {
        // Extract the fallback URL or scheme
        // Example: intent://pay?pa=...#Intent;scheme=upi;package=com.phonepe.app;end
        const schemeMatch = url.match(/scheme=([^;]+)/);
        const scheme = schemeMatch ? schemeMatch[1] : "upi";

        // Reconstruct as a normal app scheme URL
        const newUrl = url.replace("intent://", `${scheme}://`);
        Linking.openURL(newUrl).catch(() => {
          console.log("Could not open intent URL");
        });
      } catch (e) {
        console.log("Error parsing intent", e);
      }
      return false; // Block WebView from trying to load intent://
    }

    // 3. Handle standard app schemes directly (upi://, phonepe://, gpay://, etc.)
    Linking.openURL(url).catch(() => {
      console.log("Could not open URL: ", url);
    });
    return false; // Block WebView from trying to load these
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#1e3c72" />

      {/* Header */}
      <View style={styles.header}>
        <LinearGradient colors={["#1e3c72", "#2a5298"]} style={styles.headerGradient} />
        <View style={styles.headerContent}>
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => {
              if (canGoBack && webViewRef.current) {
                webViewRef.current.goBack();
              } else {
                router.back();
              }
            }}
          >
            <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Secure Payment</Text>
          <View style={styles.secureBadge}>
            <FontAwesome5 name="lock" size={14} color="#4caf50" />
          </View>
        </View>

        {/* Payment amount bar */}
        <View style={styles.amountBar}>
          <Text style={styles.amountLabel}>Paying</Text>
          <Text style={styles.amountValue}>₹{parseFloat(amount).toLocaleString()}</Text>
          <Text style={styles.studentLabel}>{student_name}</Text>
        </View>
      </View>

      <WebView
        ref={webViewRef}
        source={{ uri: paymentUrl }}
        style={styles.webview}
        originWhitelist={["*"]}
        onNavigationStateChange={handleNavigationChange}
        onShouldStartLoadWithRequest={handleShouldStartLoad}
        onLoadStart={() => setIsLoading(true)}
        onLoadEnd={() => setIsLoading(false)}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        startInLoadingState={true}
        allowsBackForwardNavigationGestures={true}
        mixedContentMode="always"
        setSupportMultipleWindows={false}
        injectedJavaScript={`
          (function() {
            // Override window.open so Netbanking pages open inside this WebView
            // instead of trying to open a new tab/window which gets blocked.
            var _origOpen = window.open;
            window.open = function(url, target, features) {
              if (url && url !== '' && url !== 'about:blank') {
                window.location.href = url;
                return null;
              }
              return _origOpen.call(window, url, target, features);
            };

            // Intercept <a target="_blank"> links too
            document.addEventListener('click', function(e) {
              var el = e.target;
              while (el && el.tagName !== 'A') el = el.parentElement;
              if (el && el.tagName === 'A' && el.target === '_blank' && el.href) {
                e.preventDefault();
                window.location.href = el.href;
              }
            }, true);
            true;
          })();
        `}
        // No custom user agent so PhonePe treats it as standard Android WebView
        renderLoading={() => (
          <View style={styles.loadingOverlay}>
            <ActivityIndicator size="large" color="#1e3c72" />
            <Text style={styles.loadingText}>Loading payment gateway...</Text>
          </View>
        )}
      />

      {/* Loading indicator overlay */}
      {isLoading && (
        <View style={styles.loadingOverlay}>
          <ActivityIndicator size="large" color="#1e3c72" />
          <Text style={styles.loadingText}>Connecting to PhonePe...</Text>
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#f8f9fa" },
  header: { backgroundColor: "#1e3c72", overflow: "hidden" },
  headerGradient: { position: "absolute", left: 0, right: 0, top: 0, bottom: 0 },
  headerContent: {
    flexDirection: "row", alignItems: "center", justifyContent: "space-between",
    paddingHorizontal: 20, paddingTop: 16, paddingBottom: 8,
  },
  backButton: {
    width: 40, height: 40, borderRadius: 20, backgroundColor: "rgba(255,255,255,0.2)",
    justifyContent: "center", alignItems: "center",
  },
  headerTitle: { fontSize: 18, fontWeight: "bold", color: "#ffffff" },
  secureBadge: {
    width: 40, height: 40, borderRadius: 20, backgroundColor: "rgba(255,255,255,0.15)",
    justifyContent: "center", alignItems: "center",
  },
  amountBar: {
    flexDirection: "row", alignItems: "center", justifyContent: "center",
    paddingHorizontal: 20, paddingBottom: 14, gap: 8,
  },
  amountLabel: { fontSize: 14, color: "rgba(255,255,255,0.7)" },
  amountValue: { fontSize: 18, fontWeight: "bold", color: "#ffffff" },
  studentLabel: { fontSize: 13, color: "rgba(255,255,255,0.7)" },
  webview: { flex: 1 },
  loadingOverlay: {
    ...StyleSheet.absoluteFillObject, backgroundColor: "rgba(255,255,255,0.95)",
    justifyContent: "center", alignItems: "center", zIndex: 10,
  },
  loadingText: { marginTop: 16, fontSize: 16, color: "#1e3c72", fontWeight: "500" },
});
