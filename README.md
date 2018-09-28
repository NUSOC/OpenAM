



Setup
=
In the .env file
-
Set up the following environment values and ensure they're correct. 
```
OPENAM_SESSION_COOKIE=SSOToken
OPENAM_BASE_URL=https://your.server.address:443
OPENAM_URL_VALIDATE=https://your.server.address:443/amserver/identity/isTokenValid?tokenid=
OPENAM_URL_GETDEETS=https://your.server.address:443/amserver/identity/attributes?subjectid=
OPENAM_URL_LOGIN=https://your.server.address/amserver/UI/Login?goto=
OPENAM_URL_REUTRN=http://your.main.url:8000
OPENAM_ALLOWED_NETIDS=abc123,xyz890,það567
```

In composer.json add
-
```
 "repositories": [
        {
            "url": "https://github.com/NUSOC/OpenAM.git",
            "type": "git"
        }
    ],
```

In the console
-
`composer require soc/openam-module`

In a controller or an Event Subscriber that fires before the controllers. 
- 
```
use SoC\OpenAM\OpenAM;
```
and 
```
$o = new OpenAM();
```


What this component does not do:
-
Because I'm trying to keep this small

- It does not connect to symfony's authentication system. 
- It does not contain various roles. Rather, it's a list of netids that are allowed in. 
- It does not save any user into a database. 

 

