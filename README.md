# telldus-nfc

Proxy service with a simple API for ad-hoc interaction with the Telldus home automation system. Useful for devices incapable of authenticating themselves against the official Telldus API (using <code>OAuth</code>).

Originally intented for providing short URLs to store in <code>ISO/IEC 14443</code> compliant low-memory NFC tags, but can be used wherever Telldus interaction through plain HTTP/1.1 is required.

## Installation
1. Enter your Telldus API credentials in <code>TelldusCredentials.php.example</code> and rename the file to <code>TelldusCredentials.php</code>.
2. Set your web server's webroot to the <code>public/</code> folder.

## Usage
All API endpoints are accessible using HTTP GET.

### /devices
Lists all registered devices.

Returns a success indicator, and an array of registered devices.

##### Request
```HTTP
GET /devices
```

##### Response
```JSON
{
  "success": true,
  "devices": [
    {
      "id": "1",
      "name": "Livingroom Lights"
    },
    {
      "id": "2",
      "name": "Coffee Maker"
    },
  ]
}
```

### /device/{id}/on
Sends the <code>turnOn</code> command to the device.

Returns a success indicator.

##### Request
```HTTP
GET /device/1/on
```

##### Response
```JSON
{
  "success": true
}
```

### /device/{id}/off
Sends the <code>turnOff</code> command to the device.

Returns a success indicator.

##### Request
```HTTP
GET /device/1/off
```

##### Response
```JSON
{
  "success": true
}
```

### /device/{id}/toggle
Sends either the <code>turnOn</code> or the <code>turnOff</code> command to the device, depending on the last command received by the device.

Returns a success indicator, and what action was taken (one of <code>on</code> or <code>off</code>).

##### Request
```HTTP
GET /device/1/toggle
```

##### Response
```JSON
{
  "success": true,
  "action": "on"
}
```
