# robote-bannerdehi
ربات (تلگرام) بنردهی


# نحوه راه اندازی
1- یک دیتابیس در هاست خود ایجاد کنید و کوئری زیر را در آن اجرا کنید
```
CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `user_id` int(50) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `message_id` int(50) NOT NULL DEFAULT '-1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
```

2- فایل ایندکس دات پی اچ پی را دانلود کرده و ادیت کنید .
بخش هایی که نیاز به تغییر دارند ، کامنت گذاری شده اند.


3- فایل تغییر داده شده را در هاست خود آپلود کنید و در کنار همین فایل یک دایرکتوری با اسم 

users 

ایجاد کنید و هیچ فایلی درون این دایرکتوری قرار ندهید


4- حالا کافیست ربات تلگرامی خودتونو بسازید و وب هوک را تنظیم کنید


موفق باشید :)

https://amirhn.ir
