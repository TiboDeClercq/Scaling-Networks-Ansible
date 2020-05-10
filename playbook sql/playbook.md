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

#### MySQL installeren

We maken de playbook "install.yml" aan. In deze playbook verwijzen we naar de mysql role die we eerder hadden geinstaleerd en maken een nieuwe bestand aan om onze variabelen in te schrijven. 

**Playbook**

```yml
- name: instaleer mysql
  hosts: databank
  become: yes
  vars_files:
    - vars/var.yml
  roles:
    - { role: geerlingguy.mysql }
```

**Variabelen**

```yml
mysql_root_password: "kroepoek"
```

**Inventory**
```
[databank]
172.17.0.2 ansible_ssh_user=root
```
Als we de playbook uitvoeren kunnen we zien dat in onze ubuntu container mysql is geïnstaleerd. Het wachtwoord is "kroepoek"

*Soms wilt mysql socket niet starten, je kan dit makkelijk poplossen met: `service mysql start`*

```bash
ubuntu# mysqladmin -uroot -p ping
Enter password:             
mysqld is alive
```
#### Aanmaken een databank

Een databank kan je gemakkelijk aanmaken met:

```yml
mysql_databases:
   - name: VoorbeeldDB
```
Je hebt de mogelijkheid om een aantal variabelen op voorhand te configureren zoals eigenaar, wachtwoord, endogin, encoding, ...

Na het uitvoeren van de playbook zien we dat er een nieuwe databank is toegevoegd.

```sql
mysql> show databases;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| VoorbeeldDB        |
| mysql              |
| performance_schema |
| sys                |
+--------------------+
5 rows in set (0.00 sec)

```

#### Aanmaken een gebruiker

Natuurlijk willen we dat de databank niet enkel toegankelijk is als root. We willen een nieuwe gebruiker toevoegen aan de databank:

```yml
mysql_users:
  - name: student
    password: t
    priv: "VoorbeeldDB.*:ALL"
```

We geven de user student op voorhand alle rechten op de VoorbeeldDB databank. Je een gebruiker op voorhand alle variabelen geven zoals je manueel zou doen bij een mysql databank (host, priviliges, ...).

Resultaat nieuwe gebruiker:

```sql
mysql> select host, user from mysql.user;
+-----------+------------------+
| host      | user             |
+-----------+------------------+
| localhost | debian-sys-maint |
| localhost | mysql.session    |
| localhost | mysql.sys        |
| localhost | root             |
| localhost | student          |
+-----------+------------------+
```
<!--
#### Andere MySQL configuraties

In sommige situaties moet je aantal specifiekere configuraties maken aan je DB. Bijvoorbeeld het veranderen van de standaar poort, beperken van aantal logins, ... Dit kan je natuurlijk ook met de mysqlModule op voorhand configureren.

Een voorbeeld:

```yml

```
-->
Bronnen:
ref: https://github.com/geerlingguy/ansible-role-postgresql