---
description: make
---

# Make

<table><thead><tr><th>Argument</th><th>Type</th><th data-type="checkbox">Required</th><th>Default</th></tr></thead><tbody><tr><td>label</td><td>string</td><td>true</td><td></td></tr><tr><td>fieldName</td><td>string</td><td>true</td><td></td></tr><tr><td>disk</td><td>string</td><td>false</td><td>public</td></tr></tbody></table>

### **Label**

This parameter allows you to define a label for the file field.

***

### **Field Name**

Specifies the name of the input element . The value you provide depends on whether you are using Larupload or not:

* **Without Larupload**: In this case, you should provide the name of the file <mark style="color:red;">column</mark> as the Field Name parameter.
* **With Larupload**: Specify the name of the Larupload <mark style="color:red;">attachment entity</mark>.

***

### **Disk**

This parameter is used to specify the name of your preferred disk as defined in the `config/filesystems.php` file.

{% hint style="info" %}
It's important to note that when you are using Larupload to handle the upload process, the <mark style="color:red;">Disk</mark> argument is not utilized.

For detailed information and configurations related to Larupload, please refer to the Larupload [documentation](https://github.com/mostafaznv/larupload).
{% endhint %}



