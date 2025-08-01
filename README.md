# YITH-WooCommerce-Affiliates-UPI-Payment-Gateway-Extension
This plugin adds UPI Payment as a custom withdrawal method for the (YITH WooCommerce Affiliates) plugin. It allows affiliates to request withdrawals using their UPI ID (e.g., Google Pay, PhonePe, Paytm, etc.).

---

## 🚀 Features

- Adds "UPI Payment" as a withdrawal method for affiliates.
- Affiliates can select their UPI provider and enter their UPI ID.
- Admins can manually mark affiliate payments as completed via UPI.
- Compatible with YITH WooCommerce Affiliates plugin logic.
- Simple configuration via affiliate settings.

---

## 📦 Installation

1. Ensure you have the **YITH WooCommerce Affiliates** plugin installed and activated.
2. Copy the provided PHP code into a custom plugin or your theme's `functions.php`.
3. Alternatively, create a standalone plugin file (e.g., `yith-upi-gateway.php`) and place the code inside it.

---

## 🛠 How It Works

- A new gateway ID `upi` is registered via the `yith_wcaf_payment_gateways` filter.
- A new class `YITH_WCAF_UPI_Gateway` extends the core `YITH_WCAF_Abstract_Gateway`.
- Affiliates can select their UPI provider and set a UPI ID from their profile.
- When processing a payment manually, the system logs the UPI details and marks the payment as completed.

---

## 📋 UPI Providers Supported

- Google Pay
- PhonePe
- Amazon Pay
- Paytm
- BHIM UPI
- Custom UPI ID

---

## ✅ Example Use Case

- Affiliate requests withdrawal.
- Admin selects UPI as the method.
- Admin processes the payment externally (via mobile banking or UPI app).
- The plugin marks the affiliate payment as completed and logs the UPI ID used.

---

## 🔒 Note

This plugin **does not perform actual UPI transactions**. It is designed to track and manage UPI withdrawal requests inside the affiliate dashboard and admin panel. Payments must be processed manually via your UPI app.

