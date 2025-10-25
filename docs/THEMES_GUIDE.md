# Theme System Documentation

## نظام مدیریت تم‌ها - Theme Management System

ExpenseLogger از یک سیستم مدیریت تم پویا استفاده می‌کند که اضافه کردن تم‌های جدید را بسیار ساده می‌کند.

## ساختار فایل‌ها - File Structure

```
config/
  ├── themes.php           # تعریف همه تم‌ها
  └── theme-generator.php  # تولیدکننده CSS
includes/
  └── header.php          # CSS خودکار تولید شده
api/
  └── theme.php           # API تعویض تم
settings.php              # صفحه تنظیمات با کارت‌های خودکار
```

## افزودن تم جدید - Adding a New Theme

### روش 1: از رابط کاربری (UI)

1. به صفحه **Settings** بروید
2. دکمه **"Add Custom Theme"** را کلیک کنید
3. کد CSS تم خود را در textarea بگذارید
4. روی **"Add Theme"** کلیک کنید
5. تم شما خودکار اضافه می‌شود!

**فرمت پشتیبانی شده:**
```css
@plugin "daisyui/theme" {
  name: "my-theme";
  color-scheme: "dark";
  --color-base-100: #1a1a1a;
  --color-primary: #3b82f6;
  /* ... rest of colors */
  --radius-box: 1rem;
}
```

### روش 2: ویرایش مستقیم فایل

### مرحله 1: تعریف تم در `config/themes.php`

فقط یک آرایه جدید به فایل اضافه کنید:

```php
'my_theme' => [
    'name' => 'My Theme',              // نام نمایشی
    'description' => 'Beautiful theme', // توضیحات
    'scheme' => 'dark',                 // 'light' or 'dark' - خودکار تشخیص داده می‌شود!
    
    'colors' => [
        'base-100' => '#1a1a1a',       // رنگ پس‌زمینه اصلی
        'base-200' => '#2a2a2a',       // پس‌زمینه ثانویه
        'base-300' => '#3a3a3a',       // پس‌زمینه سوم
        'base-content' => '#ffffff',    // رنگ متن
        'primary' => '#3b82f6',        // رنگ اصلی
        'primary-content' => '#ffffff', // متن روی primary
        'secondary' => '#8b5cf6',      // رنگ ثانویه
        'secondary-content' => '#ffffff',
        'accent' => '#ec4899',         // رنگ تاکید
        'accent-content' => '#ffffff',
        'neutral' => '#1f2937',        // خنثی
        'neutral-content' => '#ffffff',
        'info' => '#0ea5e9',           // اطلاعات
        'info-content' => '#ffffff',
        'success' => '#10b981',        // موفقیت
        'success-content' => '#ffffff',
        'warning' => '#f59e0b',        // هشدار
        'warning-content' => '#000000',
        'error' => '#ef4444',          // خطا
        'error-content' => '#ffffff',
    ],
    
    'rounded' => [
        'box' => '1rem',               // گوشه‌های کارت‌ها
        'btn' => '0.5rem',             // گوشه‌های دکمه‌ها
        'badge' => '1rem',             // گوشه‌های badge
    ],
    
    'glow' => false,                   // افکت نئونی (اختیاری - فقط برای تم neon)
],
```

### مرحله 2: تمام!

این همه چیزی است که نیاز دارید! سیستم به صورت خودکار:

✅ CSS تم را تولید می‌کند  
✅ کارت پیش‌نمایش را در settings.php ایجاد می‌کند  
✅ تم را به API اضافه می‌کند  
✅ rounded corners را به درستی اعمال می‌کند

## حذف تم سفارشی - Deleting Custom Themes

برای حذف تم‌های سفارشی:

1. به صفحه **Settings** بروید
2. موس را روی کارت تم سفارشی ببرید
3. دکمه قرمز **حذف** (🗑️) ظاهر می‌شود
4. روی آن کلیک کنید و تایید کنید

⚠️ **تم‌های پیش‌فرض قابل حذف نیستند**

تم‌های محافظت شده:
- Light, Sunset, Midnight, Forest, Pastel
- Ocean, Lavender, Autumn, Minimal, Neon, Retro

## ویژگی‌های پیشرفته - Advanced Features

### تشخیص خودکار Scheme

سیستم **خودکار** تشخیص می‌دهد تم شما روشن است یا تیره:

```php
// محاسبه روشنایی base-100
$brightness = getColorBrightness($colors['base-100']);
// اگر > 128 → light
// اگر <= 128 → dark
```

✅ اگر `base-100` روشن باشد → scheme: light  
✅ اگر `base-100` تیره باشد → scheme: dark  
✅ کارت پیش‌نمایش خودکار با همان رنگ‌ها ساخته می‌شود

### افکت Glow (نئونی)

برای تم‌های نئونی، `glow: true` را اضافه کنید:

```php
'neon' => [
    // ... colors and rounded
    'glow' => true,  // 🌟 افکت درخشش
],
```

### رنگ‌های OKLch

می‌توانید از فرمت `oklch()` برای رنگ‌های دقیق‌تر استفاده کنید:

```php
'primary' => 'oklch(74.703% 0.158 39.947)',
```

### Rounded Corners سفارشی

مقادیر مختلف `rounded-box` به صورت خودکار به کلاس‌های مناسب تبدیل می‌شوند:

- `2.5rem` → `rounded-3xl`
- `2rem` → `rounded-2xl`
- `1.5rem` → `rounded-2xl`
- `1rem` → `rounded-xl`
- `0.75rem` → `rounded-lg`
- `0.5rem` → `rounded-md`
- `< 0.5rem` → `rounded-sm`

## تم‌های موجود - Available Themes

1. **Light** - روشن و ساده
2. **Sunset** - گرم و پرانرژی
3. **Midnight** - آبی تیره
4. **Forest** - سبز طبیعی
5. **Pastel** - ملایم و نرم
6. **Ocean** - دریایی عمیق
7. **Lavender** - بنفش شیک
8. **Autumn** - پاییزی گرم
9. **Minimal** - مینیمال خاکستری
10. **Neon** - سایبرپانک درخشان
11. **Retro** - سینت‌ویو دهه 80

## API Usage

```javascript
// تعویض تم
fetch('/api/theme.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ theme: 'my_theme' })
});
```

## نکات مهم - Important Notes

⚠️ **هرگز مستقیماً `header.php` را ویرایش نکنید!**  
   همه تغییرات در `config/themes.php` انجام شود.

⚠️ **هرگز مستقیماً `settings.php` را برای افزودن کارت‌ها ویرایش نکنید!**  
   کارت‌ها به صورت خودکار از `themes.php` تولید می‌شوند.

⚠️ **رنگ‌ها را با دقت انتخاب کنید**  
   مطمئن شوید contrast کافی بین متن و پس‌زمینه وجود دارد.

## مثال کامل - Complete Example

### مثال 1: Cyberpunk Theme (از UI)

```css
@plugin "daisyui/theme" {
  name: "cyberpunk";
  color-scheme: "dark";
  --color-base-100: #0a0e1a;
  --color-base-200: #0f1420;
  --color-base-300: #141a2a;
  --color-base-content: #00ffff;
  --color-primary: #ff0080;
  --color-primary-content: #000000;
  --color-secondary: #00ffff;
  --color-secondary-content: #000000;
  --color-accent: #ffff00;
  --color-accent-content: #000000;
  --color-neutral: #0a0e1a;
  --color-neutral-content: #00ffff;
  --color-info: #00d4ff;
  --color-info-content: #000000;
  --color-success: #00ff88;
  --color-success-content: #000000;
  --color-warning: #ffea00;
  --color-warning-content: #000000;
  --color-error: #ff0080;
  --color-error-content: #ffffff;
  --radius-box: 0rem;
  --radius-field: 0rem;
}
```

این کد را کپی کنید، در modal بگذارید و "Add Theme" کنید!

### مثال 2: کد PHP مستقیم

```php
'cyberpunk' => [
    'name' => 'Cyberpunk',
    'description' => 'Future is now',
    'scheme' => 'dark',
    'colors' => [
        'base-100' => '#0a0e1a',
        'base-200' => '#0f1420',
        'base-300' => '#141a2a',
        'base-content' => '#00ffff',
        'primary' => '#ff0080',
        'primary-content' => '#000000',
        'secondary' => '#00ffff',
        'secondary-content' => '#000000',
        'accent' => '#ffff00',
        'accent-content' => '#000000',
        // ... بقیه رنگ‌ها
    ],
    'rounded' => [
        'box' => '0rem',      // بدون گوشه گرد
        'btn' => '0rem',
        'badge' => '0rem',
    ],
    'glow' => true,
],
```

## استفاده در کد - Usage in Code

```php
// دریافت لیست تم‌ها
$themes = require 'config/themes.php';

// دریافت اطلاعات یک تم
$themeData = getThemeData('midnight');

// دریافت لیست نام تم‌ها
$themeKeys = getAllThemeKeys();
```

---

**ساخته شده با ❤️ برای ExpenseLogger**
