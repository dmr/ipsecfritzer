# your local network ip range
localNetwork = '192.168.1'

# the domain, used for all account names
domain = 'domain.com'

# define if everything should be routed and encrypted through the vpn gateway (even internet traffic)
routeAllTrafficTroughVPN = True

accounts = {
    'bernd': {'localIP': 201, 'secret': 'sdg67g7gfjashfisdf', 'password': 'geheim'},
    'bernd-mobile': {'localIP': 202, 'secret': 'sdg67g7gfjashfisdf', 'password': 'geheim'},
}
