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
Dit commando gaat op de ubuntu machine openssh installeren en houdt ze draaient in de achtergrond.<br>
`
docker run -dt ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server; service ssh start; while true; do sleep 60; echo keepalive; done"
`<br>
**Centos**
TODO: commando
**Ansible**
Dit commando gaat de ubuntue machine met ansible blijven doen draaien.<br>
`
docker run -dt williamyeh/ansible:ubuntu14.04-onbuild bash -c "while true; do sleep60; echo keepalive; done"
`<br>
Controleer met **docker ps** of de containers draaien.
<br>
3. Om het ons wat makkelijker te maken gaan we gebruiken van een shell functie die van container gaat wisselen en zet het woord van de container in de prompt.<br>
*Je moet de naam van de containers aanpassen naar die van jouw.*<br>
Ubuntu: <br>
`
u() {  docker exec -it ubuntu_container bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash";  }
`
Centos:<br>
TODO: centos implementatie
Ansible:<br>
` 
a() {  docker exec -it ansible_container bash -c "echo 'PS1='\''ansible# '\' >> /root/.bashrc; bash";  }
`
<br>
4. Normaal gezien kan je lokaal naar de 