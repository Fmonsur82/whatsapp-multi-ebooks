# Text messages webhook reference



This reference describes trigger events and payload contents for the WhatsApp Business account **messages** webhook for messages containing only text.

## Triggers

- A WhatsApp user sends a text message to a WhatsApp Business phone number.
- A WhatsApp user forwards a text message to a business phone number.
- A WhatsApp user uses the **Message business** button in a [catalog, single-, or multi-product message](https://developers.facebook.com/documentation/business-messaging/whatsapp/catalogs/catalogs-overview) to send a message to the business.
- A WhatsApp user sends a text message to a business via a [Click to WhatsApp ad](https://www.facebook.com/business/help/447934475640650?id=371525583593535) (an ad with a WhatsApp **message destination**).

## Syntax

```html
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "<WHATSAPP_BUSINESS_ACCOUNT_ID>",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "<BUSINESS_DISPLAY_PHONE_NUMBER>",
              "phone_number_id": "<BUSINESS_PHONE_NUMBER_ID>"
            },
            "contacts": [
              {
                "profile": {
                  "name": "<WHATSAPP_USER_PROFILE_NAME>"
                },
                "wa_id": "<WHATSAPP_USER_ID>",
                "identity_key_hash": "<IDENTITY_KEY_HASH>" <!-- only included if identity change check enabled -->
              }
            ],
            "messages": [
              {
                "from": "<WHATSAPP_USER_PHONE_NUMBER>",
                "id": "<WHATSAPP_MESSAGE_ID>",
                "timestamp": "<WEBHOOK_TRIGGER_TIMESTAMP>",
                "type": "text",
                "text": {
                  "body": "<MESSAGE_TEXT_BODY>"
                },

                <!-- only if message originated from a "Message business" button -->
                "context": {
                  "from": "<BUSINESS_DISPLAY_PHONE_NUMBER>",
                  "id": "<CONTEXTUAL_WHATSAPP_MESSAGE_ID>",
                  "referred_product": {
                    "catalog_id": "<PRODUCT_CATALOG_ID>",
                    "product_retailer_id": "<PRODUCT_ID>"
                  }
                },

                <!-- only if message forwarded to business by a user -->
                "context": {
                  "forwarded": true,            <!-- only included if forwarded 5 times or less -->
                  "frequently_forwarded": true  <!-- only included if forwarded more than 5 times -->
                },

                <!-- only included if message sent via a Click to WhatsApp ad -->
                "referral": {
                  "source_url": "<AD_URL>",
                  "source_id": "<AD_ID>",
                  "source_type": "ad",
                  "body": "<AD_PRIMARY_TEXT>",
                  "headline": "<AD_HEADLINE>",
                  "media_type": "<AD_MEDIA_TYPE>",
                  "image_url": "<AD_IMAGE_URL>",
                  "video_url": "<AD_VIDEO_URL>",
                  "thumbnail_url": "<AD_VIDEO_THUMBNAIL>",
                  "ctwa_clid": "<AD_CLICK_ID>",  <!-- omitted if message sent via a WhatsApp Status ad placement -->
                  "welcome_message": {
                    "text": "<AD_GREETING_TEXT>"
                  }
                }
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

## Parameters

| Placeholder | Description | Example value |
| --- | --- | --- |
| `<AD_CLICK_ID>`<br><br>_String_ | Click to WhatsApp ad click ID.<br><br>The `ctwa_clid` property is omitted entirely for messages originating from an ad in WhatsApp Status ([WhatsApp Status ad placements](https://www.facebook.com/business/help/1074444721456755)). | `Aff-n8ZTODiE79d22KtAwQKj9e_mIEOOj27vDVwFjN80dp4_0NiNhEgpGo0AHemvuSoifXaytfTzcchptiErTKCqTrJ5nW1h7IHYeYymGb5K5J5iTROpBhWAGaIAeUzHL50` |
| `<AD_GREETING_TEXT>`<br><br>_String_ | Click to WhatsApp ad greeting text. | `Hi there! Let us know how we can help!` |
| `<AD_HEADLINE>`<br><br>_String_ | Click to WhatsApp ad headline. | `Chat with us` |
| `<AD_ID>`<br><br>_String_ | Click to WhatsApp ad ID. | `120226305854810726` |
| `<AD_IMAGE_URL>`<br><br>_String_ | Click to WhatsApp ad image URL. Only included if the ad is an image ad. | `https://scontent.xx.fbcdn.net/v/t45.1...` |
| `<AD_MEDIA_TYPE>`<br><br>_String_ | Click to WhatsApp ad media type. Values can be:<br><br>`image` — Indicates an image ad.<br><br>`video` — Indicates a video ad. | `image` |
| `<AD_PRIMARY_TEXT>`<br><br>_String_ | Click to WhatsApp ad primary text. | `Summer succulents are here!` |
| `<AD_URL>`<br><br>_String_ | Click to WhatsApp ad URL. | `https://fb.me/3cr4Wqqkv` |
| `<AD_VIDEO_THUMBNAIL>`<br><br>_String_ | Click to WhatsApp ad video thumbnail URL. Only included if ad is a video ad. | `https://scontent.xx.fbcdn.net/v/t45.3...` |
| `<AD_VIDEO_URL>`<br><br>_String_ | Click to WhatsApp ad video URL. Only included if ad is a video ad. | `https://scontent.xx.fbcdn.net/v/t45.2...` |
| `<BUSINESS_DISPLAY_PHONE_NUMBER>`<br><br>_String_ | Business display phone number. | `15550783881` |
| `<BUSINESS_PHONE_NUMBER_ID>`<br><br>_String_ | Business phone number ID. | `106540352242922` |
| `<CONTEXTUAL_WHATSAPP_MESSAGE_ID>`<br><br>_String_ | WhatsApp message ID of the message the WhatsApp user used to access the Message business button. | `wamid.HBgLMTY1MDM4Nzk0MzkVAgARGA9wcm9kdWN0X2lucXVpcnkA` |
| `<IDENTITY_KEY_HASH>`<br><br>_String_ | Identity key hash. Only included if you have enabled the [identity change check](https://developers.facebook.com/documentation/business-messaging/whatsapp/business-phone-numbers/phone-numbers) feature. | `DF2lS5v2W6x=` |
| `<MESSAGE_TEXT_BODY>`<br><br>_String_ | Text body of the message. | `Is it available in another color?` |
| `<PRODUCT_CATALOG_ID>`<br><br>_String_ | [Product catalog ID](https://developers.facebook.com/documentation/business-messaging/whatsapp/catalogs/catalogs-overview). | `194836987003835` |
| `<PRODUCT_ID>`<br><br>_String_ | [Product ID](https://developers.facebook.com/documentation/business-messaging/whatsapp/catalogs/catalogs-overview). | `di9ozbzfi4` |
| `<WEBHOOK_TRIGGER_TIMESTAMP>`<br><br>_String_ | Unix timestamp indicating when the webhook was triggered. | `1739321024` |
| `<WHATSAPP_BUSINESS_ACCOUNT_ID>`<br><br>_String_ | WhatsApp Business Account ID. | `102290129340398` |
| `<WHATSAPP_MESSAGE_ID>`<br><br>_String_ | WhatsApp message ID. | `wamid.HBgLMTY1MDM4Nzk0MzkVAgASGBQzQUFERjg0NDEzNDdFODU3MUMxMAA=` |
| `<WHATSAPP_USER_ID>`<br><br>_String_ | WhatsApp user ID. Note that a WhatsApp user's ID and phone number may not always match. | `16505551234` |
| `<WHATSAPP_USER_PHONE_NUMBER>`<br><br>_String_ | WhatsApp user phone number. This is the same value returned by the API as the `input` value when sending a message to a WhatsApp user. Note that a WhatsApp user's phone number and ID may not always match. | `+16505551234` |
| `<WHATSAPP_USER_PROFILE_NAME>`<br><br>_String_ | WhatsApp user's name as it appears in their profile in the WhatsApp client. | `Sheena Nelson` |

## Examples

### Text message

This example describes a text message sent by a WhatsApp user (the user just typed something into the chat field and sends).

```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "102290129340398",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "15550783881",
              "phone_number_id": "106540352242922"
            },
            "contacts": [
              {
                "profile": {
                  "name": "Sheena Nelson"
                },
                "wa_id": "16505551234"
              }
            ],
            "messages": [
              {
                "from": "16505551234",
                "id": "wamid.HBgLMTY1MDM4Nzk0MzkVAgASGBQzQTRBNjU5OUFFRTAzODEwMTQ0RgA=",
                "timestamp": "1749416383",
                "type": "text",
                "text": {
                  "body": "Does it come in another color?"
                }
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

### Message business button

This example describes a text message sent by a WhatsApp user who used a **Message business** button when [viewing a single product](https://developers.facebook.com/documentation/business-messaging/whatsapp/catalogs/catalogs-overview) to send the message.

```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "419561257915477",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "15550783881",
              "phone_number_id": "106540352242922"
            },
            "contacts": [
              {
                "profile": {
                  "name": "Sheena Nelson"
                },
                "wa_id": "16505551234"
              }
            ],
            "messages": [
              {
                "context": {
                  "from": "15550783881",
                  "id": "wamid.HBgLMTY1MDM4Nzk0MzkVAgARGA9wcm9kdWN0X2lucXVpcnkA",
                  "referred_product": {
                    "catalog_id": "194836987003835",
                    "product_retailer_id": "di9ozbzfi4"
                  }
                },
                "from": "16505551234",
                "id": "wamid.HBgLMTY1MDM4Nzk0MzkVAgASGBQzQTA2NTUwRkNEMDdFQjJCRUU0NQA=",
                "timestamp": "1750016800",
                "text": {
                  "body": "Is this still available?"
                },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

### Click to WhatsApp ad

This example describes a text message sent by a WhatsApp user who tapped a [Click to WhatsApp ad](https://www.facebook.com/business/help/447934475640650) and sent the generated message to the business.

Note that for messages originating from an ad in WhatsApp Status ([WhatsApp Status ad placements](https://www.facebook.com/business/help/1074444721456755)), the `referral.ctwa_clid` property is omitted entirely.

```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "102290129340398",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "15550783881",
              "phone_number_id": "106540352242922"
            },
            "contacts": [
              {
                "profile": {
                  "name": "Sheena Nelson"
                },
                "wa_id": "16505551234"
              }
            ],
            "messages": [
              {
                "referral": {
                  "source_url": "https://fb.me/3cr4Wqqkv",
                  "source_id": "120226305854810726",
                  "source_type": "ad",
                  "body": "Summer Succulents are here!",
                  "headline": "Chat with us",
                  "media_type": "image",
                  "image_url": "https://scontent.xx.fbcdn.net/v/t45.1...",
                  "ctwa_clid": "Aff-n8ZTODiE79d22KtAwQKj9e_mIEOOj27vDVwFjN80dp4_0NiNhEgpGo0AHemvuSoifXaytfTzcchptiErTKCqTrJ5nW1h7IHYeYymGb5K5J5iTROpBhWAGaIAeUzHL50",
                  "welcome_message": {
                    "text": "Hi there! Let us know how we can help!"
                  }
                },
                "from": "16505551234",
                "id": "wamid.HBgLMTY1MDM4Nzk0MzkVAgASGBQzQUQ0N0VFMDA2MTQ0RkJFNkNDNAA=",
                "timestamp": "1750275992",
                "text": {
                  "body": "Can I get more info about this?"
                },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
``` 