# Docker en Ansible
We gaan een aantal docker containers opzetten. Zo kunnen we gemakkelijk meerdere hosts beheren met ansible.

#### Opzetten van de docker containers
>**Tip:** maak gebruik de shell Fish, deze shell vult automatisch aan. Zo is het makkelijker om dockers te selecteren.

1. Haal met het **docker pull** commando de volgende containers binnen: 
- ubuntu:latest 
- williamyeh/ansible:ubuntu14.04-onbuild
- centos:latest

2. Nu gaan we dockers in de achtergrond draaien met volgende commando's:
**Ubuntu**
Dit commando gaat op de ubuntu machine openssh installeren en houdt ze draaient in de achtergrond. We noemen deze container ubuntu1<br>
`
docker run -dt --name ubuntu1 ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server; service ssh start; while true; do sleep 60; echo keepalive; done"
`

**Centos**

TODO: commando

**Ansible**

Dit commando gaat de ubuntu machine met ansible blijven doen draaien. We noemen de container ansible1<br>

`
docker run -dt --name ansible1 williamyeh/ansible:ubuntu14.04-onbuild  bash -c "while true; do sleep60; echo keepalive; done"
`

Controleer met **docker ps** of de containers draaien.

3. Om het ons wat makkelijker te maken gaan we gebruiken van een shell functie die van container gaat wisselen en zet het woord van de container in de prompt.

Ubuntu: 

`
u() {  docker exec -it ubuntu1 bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash";  }
`

Centos:

TODO: centos implementatie

Ansible:

` 
a() {  docker exec -it ansible1 bash -c "echo 'PS1='\''ansible# '\' >> /root/.bashrc; bash";  }
`


4. Normaal gezien kan je lokaal naar de 