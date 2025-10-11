import { Stack } from "expo-router";

export default function RootLayout() {
  return (
  <Stack>
   <Stack.Screen name="Dashboard" options={{ headerShown: false }} />
    <Stack.Screen name="Index" options={{ headerShown: false }} />
    <Stack.Screen name="SearchStudent" options={{ headerShown: false }} />
    <Stack.Screen name="PayFee" options={{ headerShown: false }} />
    <Stack.Screen name="PaymentConfirmationScreen" options={{ headerShown: false }} />
    <Stack.Screen name="ReceiptScreen" options={{ headerShown: false }} />
    <Stack.Screen name="CollectionReport" options={{ headerShown: false }} />
    <Stack.Screen name="Attendance" options={{ headerShown: false }} />
   
  </Stack>
  );
}
