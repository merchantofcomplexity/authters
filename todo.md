#fix
. allow empty auth key from config and handle count services  on all services

. recaller need to be rewrite as logout

. fix lockout response for http basic auth for too many login attempts 
by default we use Authentication exception which is caught by the http basic entrypoint

. fix switch identity where enforce token clear the token source

#checkMe
. Authentication service failure need to be handled differently from authentication exception
in debug firewall handler

. Local authentication middleware wont accept a re authentication of a remembered
identity unless we remove the verification of storage

#todo

. need to handle exception correctly @see symfony http

. setup security errors bag and session

. implement identity status enabled , suspended etc etc

. identity credentials need to be rewrite cf symfony

. token clock, has user changed
. token bis check serialization and implement a correct to json 

#authorization

. rewrite symfony role hierarchy if doable
. logout exception in debug handler

#recaller

. need default encoder and recaller key also need it in firewall context

# helpers directives

# roles
. make getRoles return iterable instead of array
will also need to handle collection in token HasConstructorRoles 

# Model identifier
. cons: identifier value as array must have to fit the model attribute

#todo
. registries could be overridden per firewall in config

. permissions

. 2fa

. recaptcha

. tests

