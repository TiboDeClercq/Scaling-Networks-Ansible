# SQL ansible playbook

We gaan gebruik maken van een ubuntu container:

```bash
docker run -dt --name ubuntu1 ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

```bash
u1() { docker exec -it ubuntu1 bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash"; }
```

Kopieer de pub ssh-key van je ansible host naar de docker container.
In de inventory zetten we het ip adres van de ubuntu machine.

**Ansible Role**

Om een Mysql op te zetten zijn heel veel stappen nodig (MySQL dependencies, post-installatie configuratie,..). Deze stappen nemen veel tijd. Daarom gaan we gebruik maken van een MySQL module om een veel eenvoudige en kortere playbook te schrijven.

Op Ansible Galaxy vind je verschillende MySQL roles terug die je kan gebruiken in de playbook.

In deze playbook maken we gebruik van de [volgende module](https://galaxy.ansible.com/geerlingguy/mysql).

```bash
ansible-galaxy install geerlingguy.mysql
```
