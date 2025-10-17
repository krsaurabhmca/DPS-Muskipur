import { Stack } from "expo-router";
import { SafeAreaProvider } from "react-native-safe-area-context";

export default function RootLayout() {
  return (
    <SafeAreaProvider>
      {/* <SafeAreaView style={{ flex: 1 }} edges={["top","bottom", "left", "right"]}> */}
        <Stack>
          <Stack.Screen name="index" options={{ headerShown: false }} />
          <Stack.Screen name="Dashboard" options={{ headerShown: false }} />
          <Stack.Screen name="SearchStudent" options={{ headerShown: false }} />
          <Stack.Screen name="PayFee" options={{ headerShown: false }} />
          <Stack.Screen
            name="PaymentConfirmationScreen"
            options={{ headerShown: false }}
          />
          <Stack.Screen name="ReceiptScreen" options={{ headerShown: false }} />
          <Stack.Screen
            name="CollectionReport"
            options={{ headerShown: false }}
          />
          <Stack.Screen name="Attendance" options={{ headerShown: false }} />
          <Stack.Screen name="DuesList" options={{ headerShown: false }} />
          <Stack.Screen name="Notice" options={{ headerShown: false }} />
          <Stack.Screen name="HomeWork" options={{ headerShown: false }} />
        </Stack>
      {/* </SafeAreaView> */}
    </SafeAreaProvider>
  );
}
