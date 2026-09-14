# Permissions



Platform endpoints are gated by permissions. References for each endpoint indicate which permissions it requires, but in general, you will need the following:

- [whatsapp_business_management](https://developers.facebook.com/docs/permissions#whatsapp_business_management) — needed to access metadata on your WhatsApp Business account, template management, getting business phone numbers associated with your WABA, all analytics, and to receive webhooks notifying you of changes to your WhatsApp Business account
- [whatsapp_business_messaging](https://developers.facebook.com/docs/permissions#whatsapp_business_messaging) — needed to send any type of message to WhatsApp users, and to receive incoming message and message status webhooks

Depending on your business needs, you may also need these permissions:

- [business_management](https://developers.facebook.com/docs/permissions#business_management) — only needed if you need to programmatically access your business portfolio (this is rarely needed, since you can access your portfolio using [Meta Business Suite](https://business.facebook.com/)).
- [whatsapp_business_manage_events](https://developers.facebook.com/docs/permissions#whatsapp_business_manage_events) — only needed if you are sending marketing templates with [Marketing Messages API for WhatsApp](https://developers.facebook.com/documentation/business-messaging/whatsapp/marketing-messages/overview), in conjunction with the [Conversions API](https://developers.facebook.com/documentation/ads-commerce/conversions-api), for event tracking.
- [ads_read](https://developers.facebook.com/docs/permissions#ads_read) — only needed if you are using [Marketing Messages API for WhatsApp](https://developers.facebook.com/documentation/business-messaging/whatsapp/marketing-messages/overview) in conjunction with the [Insights API](https://developers.facebook.com/documentation/ads-commerce/marketing-api/insights) to get conversion metrics.

## App Review

If you are a [partner](https://developers.facebook.com/documentation/business-messaging/whatsapp/solution-providers/overview) and your clients will be using your app to access their data, your app must undergo [App Review](https://developers.facebook.com/documentation/business-messaging/whatsapp/solution-providers/app-review), and you must be approved for **Advanced access** for any permissions your app needs. If you lack **Advanced access** for a given permission, your clients cannot grant your app that permission via Embedded Signup.

If your app uses the `whatsapp_business_management` permission to access WABAs not owned by your business, you must have **Advanced access** for that permission. Without it, API calls return error code `200`.

If you are a direct developer and only access your own business data, you do not need to undergo App Review or obtain **Advanced access** for any permissions.

## How to get permissions

App users must grant your app individual permissions. If you are a direct developer and are using a system token, when you create a [system token](https://developers.facebook.com/documentation/business-messaging/whatsapp/access-tokens#system-user-access-tokens), you must create a system user and use it to grant your app individual permissions as part of the system token creation process:

If you are a [partner](https://developers.facebook.com/documentation/business-messaging/whatsapp/solution-providers/overview) using [business tokens](https://developers.facebook.com/documentation/business-messaging/whatsapp/access-tokens#business-integration-system-user-access-tokens), the Embedded Signup [authorization screen](https://developers.facebook.com/documentation/business-messaging/whatsapp/embedded-signup/default-flow#authorization-screen) allows your client to grant your app permissions for which you have **Advanced access** approval:

## Checking for granted permissions

Use the **debug_token** endpoint to see which permissions the token granter has granted to your app. Alternatively, you can use the [access token debugger](https://developers.facebook.com/tools/debug/accesstoken/) tool, which returns the same information.

### Request syntax

```html
curl 'https://graph.facebook.com/<API_VERSION>/debug_token?input_token=<ACCESS_TOKEN_TO_CHECK>' \
-H 'Authorization: Bearer <ACCESS_TOKEN>'
```

### Response syntax

Granted permissions are assigned to the `scopes` property.

```json
{
    "data": {
        "app_id": "634974688087057",
        "type": "SYSTEM_USER",
        "application": "Lucky Shrub",
        "data_access_expires_at": 0,
        "expires_at": 0,
        "is_valid": true,
        "issued_at": 1712099387,
        "scopes": [
            "whatsapp_business_management",
            "whatsapp_business_messaging"
        ],
        "granular_scopes": [
            {
                "scope": "whatsapp_business_management"
            },
            {
                "scope": "whatsapp_business_messaging"
            }
        ],
        "user_id": "104169029247128"
    }
}
```