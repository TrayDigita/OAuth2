# OAuth2 Server & Client Library For PHP

**PSR-(3/7/18/17) compliant OAuth 2.0 library for PHP.**

Complete implementation & separation complex data structures into separate classes.
Support Server & Client side of the protocol.

[OAuth 2.0 Protocol Flow #Section-1.2](https://datatracker.ietf.org/doc/html/rfc6749#section-1.2)

```txt
     +--------+                               +---------------+
     |        |--(A)- Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<-(B)-- Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(C)-- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<-(D)----- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(E)----- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<-(F)--- Protected Resource ---|               |
     +--------+                               +---------------+
```
