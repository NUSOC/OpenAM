



Setup
-
In `.env` set up the following environment values and ensure they're correct. 


```
OPENAM_SESSION_COOKIE=SSOToken
OPENAM_BASE_URL=https://your.server.address:443
OPENAM_URL_VALIDATE=https://your.server.address:443/amserver/identity/isTokenValid?tokenid=
OPENAM_URL_GETDEETS=https://your.server.address:443/amserver/identity/attributes?subjectid=
OPENAM_URL_LOGIN=https://your.server.address/amserver/UI/Login?goto=
OPENAM_URL_REUTRN=http://your.main.url:8000
OPENAM_ALLOWED_NETIDS=abc123,xyz890,það567
```
