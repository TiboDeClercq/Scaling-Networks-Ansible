### Ansible Netwerk automatie

Met Ansible zijn de mogelijkheden oneindig. In deze playbook gaan proberen om een router te configureren. Dit doen we door gebruik maken van [Cisco devnet sandbox](https://devnetsandbox.cisco.com/RM/Topology). 

We maken gebruik van de volgende sandboxes:

- [IOS XE on CSR Latest Code Always On](https://devnetsandbox.cisco.com/RM/Diagram/Index/38ded1f0-16ce-43f2-8df5-43a40ebf752e?diagramType=Topology)

- [IOS XE on CSR Recommended Code Always On](https://devnetsandbox.cisco.com/RM/Diagram/Index/27d9747a-db48-4565-8d44-df318fce37ad?diagramType=Topology)

Onder Access Details kan je de configuratie van de routers terugvinden:

<img src="pic-selected-200518-2222-29.png"/>

We zetten de 2 routers in onze inventory volgens de Acces Details:
```bash
[routers]
ios-xe-mgmt.cisco.com 
ios-xe-mgmt-latest.cisco.com

[routers:vars]
ansible_port=8181
ansible_network_os=ios
ansible_user=developer
ansible_password=C1sco12345
ansible_connection=network_cli
```

In het bestand ansible.cnfg gaan we een kleine wijziging doen. Dit is omdat we normaal gezien telkens de ssh key moeten kopieëren. We zetten dit even af.

```bash
# uncomment this to disable SSH key host checking
host_key_checking = False
```

Nu kunnen we onze omgeving even testen met een simpele ping:

```bash
ansible routers -i inventory -m ping
```

#### Toevoegen van een banner

Je kan met het volgende commando verbinnen met de routers van de cisco sandbox.

**ios-xe-mgmt-latest.cisco.com**
```bash
ssh -oKexAlgorithms=+diffie-hellman-group14-sha1 developer@ios-xe-mgmt-latest.
cisco.com -p 8181
```
**ios-xe-mgmt.cisco.com**
```bash
ssh -oKexAlgorithms=+diffie-hellman-group14-sha1 developer@ios-xe-mgmt.cisco.com -p 8181
```

```yml
- name: Configureren van Router
  hosts: routers
  tasks:
  - name: Banner toevoegen
    ios_banner:
      banner: login
      text: Wat zijn 8 hobbits?
      state: present

  - name: loopback
    ios_interface:
      name: Loopback21
      state: present 
```

Dit is het resultaat na het uitvoeren van onze eerste playbook.

```bash
tibuaksi@tibauski ~ (master)> ssh -oKexAlgorithms=+diffie-hellman-group14-sha1 developer@ios-xe-mgmt-latest.cisco.com -p 8181

Wat zijn 8 hobbits?
Password:

Welcome to the DevNet Sandbox for CSR1000v and IOS XE

The following programmability features are already enabled:
  - NETCONF
  - RESTCONF

Thanks for stopping by.


csr1000v-1#show ip int brief
Interface              IP-Address      OK? Method Status                Protocol
GigabitEthernet1       10.10.20.48     YES NVRAM  up                    up
GigabitEthernet2       unassigned      YES NVRAM  administratively down down
GigabitEthernet3       unassigned      YES NVRAM  administratively down down
Loopback21             unassigned      YES unset  up                    up

```

We zien de nieuwe banner "Wat zijn 8 hobbits?" bij het aanmelden en de nieuwe interface.

#### Veranderen van de host name

We willen graag de hostname van de routers veranderen. Dit is zeer gemakkelijk, we doen dit met de volgende task.

```yml
- name: Verander hostname
    ios_config: 
      lines:
        - hostname Hobbit

```

resultaat:

#### Pushen van config.txt naar routers

Op onze routers willen we 8 nieuwe gebruikers, namelijk 8 hobbits.
We kunnen dit makkelijk doen door een lokale txt bestand naar al onze routers te sturen. In dit voorbeeld gaan we enkel nieuwe gebruikers aanmaken, maar je kan met deze config alles doen wat cisco gerelateerd is.

Ons bestand ziet er het volgende uit:

```
username hobbit1 password 0 kroepoek 
username hobbit2 password 0 kroepoek 
username hobbit3 password 0 kroepoek 
username hobbit4 password 0 kroepoek 
username hobbit5 password 0 kroepoek 
username hobbit6 password 0 kroepoek 
username hobbit7 password 0 kroepoek 
username hobbit8 password 0 kroepoek 
```

De playbook:

```yml
- name: Router configuratie 
  hosts: routers
  connection: network_cli
  
  tasks:
  - name: config file pushen
    ios_config:
      src: "./config.txt" #locatie van config 

  - name: opslaan als er wijzigingen worden gemaakt
    ios_config:
      save_when: modified
```

Wanneer we inloggen na het uitvoeren van de playbook het volgende commando uitvoeren zien we dat er 1 Hobyte is aangemaakt.

```bash
csr1000v-1#sh run | inc username
username developer privilege 15 secret 9 $9$dfqsdfXi6Xgg438iAE$..VhXRCHiDQy3K2zmZLUlG9iZLbEQJ9wpUc2
username cisco privilege 15 secret 9 $9$COf3Q4xfzT.JxE$L3qsfqsdfhvSkDv88Hzdv/rPQjLNOjreYG2ocffHG7rls
username root privilege 15 secret 9 $9BFgcs03AE$MyLIWEuiRle8p3yGflAsGTcrJA6BdqsdfUUh/oWtyyfoMQXSI
username hobbit1 password 0 kroepoek
username hobbit2 password 0 kroepoek
username hobbit3 password 0 kroepoek
username hobbit4 password 0 kroepoek
username hobbit5 password 0 kroepoek
username hobbit6 password 0 kroepoek
username hobbit7 password 0 kroepoek
username hobbit8 password 0 kroepoek
```