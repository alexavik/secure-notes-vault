# 📱 Android WebView App Guide

## Complete Tutorial: Turn SNV into Native Android App

---

## 🛠️ Prerequisites

- Android Studio installed
- Java JDK 8+ installed
- Web app deployed and accessible online
- Basic understanding of Android development

---

## 🚀 Step-by-Step Guide

### Step 1: Install Android Studio

1. Download from: https://developer.android.com/studio
2. Install with default settings
3. Download SDK platforms (API 21+)

### Step 2: Create New Project

1. **Open Android Studio**
2. Click **New Project**
3. Select **Empty Activity**
4. Configure project:
   - **Name:** Secure Notes Vault
   - **Package name:** com.aviksec.notevault
   - **Save location:** Choose directory
   - **Language:** Java (or Kotlin)
   - **Minimum SDK:** API 21 (Android 5.0)
5. Click **Finish**

### Step 3: Configure AndroidManifest.xml

**File:** `app/src/main/AndroidManifest.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="com.aviksec.notevault">

    <!-- Internet permissions -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.SecureNotesVault"
        android:usesCleartextTraffic="true">
        
        <activity
            android:name=".MainActivity"
            android:configChanges="orientation|screenSize|keyboardHidden"
            android:exported="true">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>
    </application>

</manifest>
```

### Step 4: Create MainActivity (Java)

**File:** `app/src/main/java/com/aviksec/notevault/MainActivity.java`

```java
package com.aviksec.notevault;

import android.os.Bundle;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.view.KeyEvent;
import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {
    
    private WebView webView;
    private static final String WEBSITE_URL = "https://yourdomain.com/secure-notes-vault";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Initialize WebView
        webView = findViewById(R.id.webview);
        
        // Configure WebView settings
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setDatabaseEnabled(true);
        webSettings.setAllowFileAccess(false);
        webSettings.setAllowContentAccess(false);
        webSettings.setGeolocationEnabled(false);
        webSettings.setLoadsImagesAutomatically(true);
        webSettings.setMixedContentMode(WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE);
        
        // Enable caching
        webSettings.setCacheMode(WebSettings.LOAD_DEFAULT);
        webSettings.setAppCacheEnabled(true);
        
        // Set WebView clients
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                view.loadUrl(url);
                return true;
            }
        });
        
        webView.setWebChromeClient(new WebChromeClient());
        
        // Load URL
        webView.loadUrl(WEBSITE_URL);
    }

    // Handle back button
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK && webView.canGoBack()) {
            webView.goBack();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (webView != null) {
            webView.destroy();
        }
    }
}
```

### Step 5: Create Layout XML

**File:** `app/src/main/res/layout/activity_main.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<RelativeLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    tools:context=".MainActivity">

    <WebView
        android:id="@+id/webview"
        android:layout_width="match_parent"
        android:layout_height="match_parent" />

</RelativeLayout>
```

### Step 6: Update Strings

**File:** `app/src/main/res/values/strings.xml`

```xml
<resources>
    <string name="app_name">Secure Notes Vault</string>
</resources>
```

### Step 7: Add Splash Screen (Optional)

**File:** `app/src/main/res/drawable/splash_background.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
    <item android:drawable="@color/primary_color"/>
    <item>
        <bitmap
            android:gravity="center"
            android:src="@drawable/logo"/>
    </item>
</layer-list>
```

### Step 8: Configure Build.gradle

**File:** `app/build.gradle`

```gradle
plugins {
    id 'com.android.application'
}

android {
    compileSdk 33

    defaultConfig {
        applicationId "com.aviksec.notevault"
        minSdk 21
        targetSdk 33
        versionCode 1
        versionName "1.0"

        testInstrumentationRunner "androidx.test.runner.AndroidJUnitRunner"
    }

    buildTypes {
        release {
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
    
    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
    }
}

dependencies {
    implementation 'androidx.appcompat:appcompat:1.6.1'
    implementation 'com.google.android.material:material:1.9.0'
    implementation 'androidx.constraintlayout:constraintlayout:2.1.4'
}
```

### Step 9: Build APK

1. **Build Menu** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
2. Wait for build to complete
3. Click **locate** to find APK
4. APK location: `app/build/outputs/apk/debug/app-debug.apk`

### Step 10: Test on Device

#### Via USB:
1. Enable Developer Options on Android device
2. Enable USB Debugging
3. Connect device via USB
4. Click **Run** in Android Studio

#### Via APK Install:
1. Copy APK to device
2. Enable "Install from Unknown Sources"
3. Open APK and install

---

## 🎨 Customization

### Change App Icon

1. **Right-click** `res` folder
2. Select **New** → **Image Asset**
3. Choose icon type and upload image
4. Click **Next** → **Finish**

### Change App Colors

**File:** `app/src/main/res/values/colors.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <color name="primary_color">#007bff</color>
    <color name="primary_dark">#0056b3</color>
    <color name="accent_color">#28a745</color>
</resources>
```

---

## 🚀 Publishing to Google Play Store

### 1. Generate Signed APK

1. **Build** → **Generate Signed Bundle / APK**
2. Select **Android App Bundle**
3. Create new keystore (save securely!)
4. Fill keystore details
5. Select **release** build variant
6. Click **Finish**

### 2. Create Google Play Console Account

1. Visit: https://play.google.com/console
2. Pay $25 one-time fee
3. Complete account setup

### 3. Create App Listing

1. Click **Create app**
2. Fill app details:
   - App name: Secure Notes Vault
   - Category: Productivity
   - Content rating: Everyone
3. Upload screenshots (720x1280px minimum)
4. Write description
5. Upload AAB file
6. Submit for review

---

## 🔒 Security Best Practices

### WebView Security

```java
// Disable file access
webSettings.setAllowFileAccess(false);
webSettings.setAllowContentAccess(false);

// Disable geolocation
webSettings.setGeolocationEnabled(false);

// Enable safe browsing
webSettings.setSafeBrowsingEnabled(true);
```

### ProGuard Configuration

**File:** `app/proguard-rules.pro`

```proguard
-keep class android.webkit.** { *; }
-keepclassmembers class * extends android.webkit.WebViewClient {
    public void *(android.webkit.WebView, java.lang.String);
}
```

---

## 🐛 Troubleshooting

### "Webpage not available"
- Check internet permission in manifest
- Verify website URL is correct
- Test URL in mobile browser first

### "ERR_CLEARTEXT_NOT_PERMITTED"
- Add `android:usesCleartextTraffic="true"` to manifest
- Or use HTTPS only

### Back button not working
- Verify `onKeyDown` method is implemented
- Check `webView.canGoBack()` condition

---

## 📞 Support

Need help?
- 📧 Telegram: [@unknownwarrior911](https://t.me/unknownwarrior911)
- 🌐 Website: [aviksec.xo.je](https://aviksec.xo.je)

---

**Developed by Avik Maji**
© 2024 All rights reserved.