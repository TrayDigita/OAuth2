# OAuth2 Server & Client Library For PHP

**PSR-(7/18/17) compliant OAuth 2.0 library for PHP.**

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

## Todo
- [ ] Implementing [OAuth2 Requests](src/Clients/Requests)
- [ ] Completing [Abstract Client Provider](src/Abstracts/AbstractClientProvider.php)
- [ ] Creating Sample Provider (e.g. GitHub, Google, Facebook, etc.)
- [ ] Creating Sample Server (e.g. Authorization Server, Resource Server, etc.)
- [ ] Completing final classes for server implementation.
- [ ] More Features ...
