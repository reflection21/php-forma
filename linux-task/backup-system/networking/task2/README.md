apt update
apt install openvswitch-switch
systemctl enable --now openvswitch-switch


Ты создаёшь одну широкую трассу (LACP)
И разрешаешь ехать на ней разным типам машин (VLAN).


LACP и BOND — это две части одной системы
BOND — это механизм линк-агрегации в Linux.

Он объединяет несколько интерфейсов в один логический.

LACP — это протокол, который BOND может использовать.

Это протокол согласования сторонами, что они собираются работать как один агрегированный канал.



auto vmbr111
iface vmbr111 inet manual
    bridge-ports bond0.111
    bridge-stp off
    bridge-fd 0

auto vmbr112
iface vmbr112 inet manual
    bridge-ports bond0.112
    bridge-stp off
    bridge-fd 0

auto vmbr113
iface vmbr113 inet manual
    bridge-ports bond0.113
    bridge-stp off
    bridge-fd 0



Судя по изображению и тексту под ним, это задание по настройке сетевой инфраструктуры на виртуальных машинах с использованием LACP, VLAN и маршрутизации. Разберём, что конкретно нужно сделать:

1️⃣ Общая топология

4 виртуальные машины: a2-1, a2-2, a2-3, a2-4.

Между a2-1 и a2-2 нужно настроить LACP (Link Aggregation), который объединяет интерфейсы vmbr2121 и vmbr2122.

На a2-2 интерфейсы к a2-3 (vmbr223) и к a2-4 (vmbr224).

2️⃣ Сетевые параметры

VLAN:

vlan1 (ID 111): 10.0.0.0/24

vlan2 (ID 112): 10.0.1.0/24

vlan3 (ID 113): 10.0.2.0/24

На канале LACP между a2-1 и a2-2 данные должны передаваться по трём VLAN одновременно: 111, 112, 113.

3️⃣ Настройка a2-2 конкретно

Интерфейс, который идёт к a2-4, должен быть подключён через bridge.

С помощью netfilter (iptables или nftables) нужно иметь возможность включать/отключать VLAN 111 и 112 по необходимости.

Интерфейс LACP должен иметь IP из VLAN3 (10.0.2.x).

4️⃣ Трафик

Между a2-2 и a2-3 — без VLAN, т.е. чистый трафик.

Между a2-2 и a2-4 — только VLAN1 и VLAN2, т.е. трафик этих двух VLAN должен идти на a2-4.

5️⃣ Цель задания

Подготовить конфигурацию маршрутизации двумя способами:

Через конфигурационные файлы сетевых интерфейсов (например, /etc/network/interfaces или netplan на Linux VM).

Через команды в терминале (например, ip link, ip addr, bridge, vconfig и т.д.).

Конфигурация должна быть готова к проверке на виртуальных машинах.

💡 Итог:
Тебе нужно настроить LACP между a2-1 и a2-2 с тремя VLAN, настроить bridge на a2-2 к a2-4 с фильтрацией VLAN, выставить IP на LACP интерфейс из VLAN3, и подготовить маршрутизацию двумя способами.

Если хочешь, я могу набросать пошаговую схему конфигурации для всех машин с командами Linux.
Хочешь, чтобы я это сделал?



                   ┌─────────────┐
                   │   a2-1      │
                   │─────────────│
                   │ vmbr2121    │
                   │ vmbr2122    │
                   │ bond0       │
                   │ ├─ bond0.111│ VLAN 111 (10.0.0.0/24)
                   │ ├─ bond0.112│ VLAN 112 (10.0.1.0/24)
                   │ └─ bond0.113│ VLAN 113 (10.0.2.0/24)
                   └─────┬───────┘
                         │
                         │  (LACP 802.3ad)
                         │
                   ┌─────▼───────┐
                   │  SW / vSwitch│
                   │  поддерживает│
                   │   LACP       │
                   └─────┬───────┘
                         │
                         │  (LACP 802.3ad)
                         │
                   ┌─────────────┐
                   │   a2-2      │
                   │─────────────│
                   │ vmbr2121    │
                   │ vmbr2122    │
                   │ bond0       │
                   │ ├─ bond0.111│ VLAN 111 (передаётся на br224 → a2-4)
                   │ ├─ bond0.112│ VLAN 112 (передаётся на br224 → a2-4)
                   │ └─ bond0.113│ VLAN 113 (IP 10.0.2.2, маршрутизация)
                   │ vmbr223     │ к a2-3 (обычный линк)
                   │ vmbr224     │ к a2-4 через br224
                   │ br224       │ bridge для VLAN111 и VLAN112
                   └─────────────┘


# /etc/netplan/01-a2-2.yaml
network:
  version: 2
  ethernets:
    ens18: {}
    ens19: {}
  bonds:
    bond0:
      interfaces: [ens18, ens19]
      parameters:
        mode: 802.3ad
        mii-monitor-interval: 100
        lacp-rate: fast
      dhcp4: no
  vlans:
    bond0.111:
      id: 111
      link: bond0
      addresses: [10.0.0.1/24]
    bond0.112:
      id: 112
      link: bond0
      addresses: [10.0.1.1/24]
    bond0.113:
      id: 113
      link: bond0
      addresses: [10.0.2.1/24]


Маємо 4 ВМ з’єднаних згідно топології вище. Між a2-1 і a2-2 налаштувати LACP, по якому будуть передаватись дані по трьох вланах (vlan1(111) vlan2(112) vlan3(113)).

На a2-2 між LACP та інтерфейсом в сторону a2-4 має бути налаштований bridge. На a2-2 за допомогою netfilter потрібно мати змогу вмикати або забороняти vlan1, vlan2. Також на a2-2 на інтерфейсі LACP має бути налаштована IP-адреса з vlan3.

Трафік між a2-2 та a2-3 має бути без вланів
Між a2-2 та a2-4 має передаватись 2 влані (vlan1, vlan2)

Ви маєте підготувати налаштування роутингу двома способами конфігом (має бути налаштовано на віртуальних машинах) і командами з терміналу (має бути підготовлено окремо, перевірятиметься під час здачі).




ovs-vsctl add-bond vmbr2121 bond0 tap108i0 tap108i2 lacp=active other-config:bond_mode=balance-slb

ovs-vsctl add-bond vmbr2122 bond1 tap108i1 tap109i1 lacp=active other-config:bond_mode=balance-slb

если не стает бонд делать ребут

cat /proc/net/bonding/bond0 конфиг бонда









network:
  version: 2
  renderer: networkd
  ethernets:
    ens20:
      dhcp4: no
      addresses: [21.0.0.3/24]
    ens18: {}
    ens19: {}
    ens22: {}                    

  bonds:
    bond0:
      interfaces: [ens18, ens19]
      parameters:
        mode: active-backup
        primary: ens18
        mii-mon: 100

  bridges:
    br228:
      interfaces: [bond0, ens22]        
      dhcp4: no
      parameters:
        stp: false
        vlan-filtering: true           
  vlans:
    vlan103:
      id: 111
      link: bond0                      
      addresses: [10.0.]



network:
  version: 2
  ethernets:
    ens18: {}
    ens19:
      dhcp4: no
      addresses:
        - 21.0.0.5/24
  vlans:
    vlan111:
      id: 111
      link: ens18
      addresses: [10.0.0.2/24]





network:
  version: 2
  ethernets:
    ens19: {}
    ens20: {}
    ens18:
      dhcp4: no
      addresses:
        - 192.168.122.91/24
  bonds:
    bond0:
      interfaces: [ens19, ens20]
      parameters:
        mode: active-backup
        primary: ens19
  vlans:
    vlan111:
      id: 111
      link: bond0
      addresses:
        - 21.0.1.1/24
    vlan112:
      id: 112
      link: bond0
      addresses:
        - 21.0.2.1/24









        network:
  version: 2
  renderer: networkd

  ethernets:
    ens18:
      dhcp4: no
      addresses: [192.168.122.15/24]
      routes:
        - to: default
          via: 192.168.122.1

    ens19: {}
    ens20: {}
  bonds:
    bond0:
      interfaces: [ens19, ens20]
      parameters:
        mode: active-backup
        primary: ens19
  vlans:
    vlan111:
      id: 111
      link: bond0
      addresses:
        - 21.0.1.2/24
    vlan112:
      id: 112
      link: bond0
      addresses:
        - 21.0.2.2/24



network:
  version: 2
  renderer: networkd

  ethernets:
    ens18:
      addresses:
        - 192.168.122.15/24    
    ens19: {}
    ens20: {}
    ens21: {}     # тот самый "порт как свитч"

  bonds:
    bond0:
      interfaces: [ens19, ens20]
      parameters:
        mode: active-backup
        primary: ens19

  bridges:
    br0:
      interfaces: [bond0, ens21]
      parameters:
        stp: false
        forward-delay: 0

  vlans:
    vlan111:
      id: 111
      link: br0 
      addresses:
        - 21.0.1.2/24

    vlan112:
      id: 112
      link: br0
      addresses:
        - 21.0.2.2/24




sudo nft flush ruleset удаляет все правила

sudo nft list ruleset - посмотерть правила

nft add table bridge filter

nft add chain bridge filter vlan111 { type filter hook forward priority -200\; policy accept\; }

nft add rule bridge filter vlan111 vlan id 111 counter drop

sudo nft delete rule bridge filter vlan111 handle 0

nft delete rule bridge filter hello handle 0


network:
  ethernets:
    ens18:
      dhcp4: false
      addresses: 
        - 192.168.122.10/24
      routes:
        - to: 0.0.0.0/0
          via: 192.168.122.1
    ens19: {}
    ens20: {}
  bonds:
    bond0:
      interfaces: [ens19, ens20]
      addresses: [192.168.93.1/24]
      macaddress: 02:11:22:33:44:65  
      parameters:
        mode: active-backup 
  vlans:
    vlan111:
      id: 111
      link: bond0
      addresses:
        - 10.0.0.1/24
    vlan112:
      id: 112
      link: bond0
      addresses:
        - 10.0.1.1/24



network:
  version: 2
  renderer: networkd

  ethernets:
    ens18:
      addresses:
        - 192.168.122.15/24    
    ens19: {}
    ens20: {}
    ens21: {}     #порт как свитч
    ens22: 
      addresses:
        - 21.0.1.1/24  
  bonds:
    bond0:
      interfaces: [ens19, ens20]
      addresses: [192.168.93.2/24]  
      macaddress: 02:11:22:33:44:55
      parameters:
        mode: active-backup


  bridges:
    br0:
      interfaces: [bond0, ens21]
      parameters:
        stp: false
        forward-delay: 0
  vlans:
    vlan3:
      id: 113
      link: bond0
      addresses:
        - 10.0.2.254/24



network:
  version: 2
  ethernets:
    ens19: {}
    ens18:
      dhcp4: no
      addresses:
        - 192.168.122.91/24
  vlans:
    vlan111:
      id: 111
      link: ens19 
      addresses: 
        - 10.0.0.2/24
    vlan112:
      id: 112
      link: ens19
      addresses:
        - 10.0.1.2/24




root@a21:~# ethtool ens20
Settings for ens20:
	Supported ports: [  ]
	Supported link modes:   Not reported
	Supported pause frame use: No
	Supports auto-negotiation: No
	Supported FEC modes: Not reported
	Advertised link modes:  Not reported
	Advertised pause frame use: No
	Advertised auto-negotiation: No
	Advertised FEC modes: Not reported
	Speed: Unknown!
	Duplex: Unknown! (255)
	Auto-negotiation: off
	Port: Other
	PHYAD: 0
	Transceiver: internal
	Link detected: yes




#!/bin/bash
echo 100 > /sys/class/net/bond0/bonding/arp_interval
echo 192.168.93.2 > /sys/class/net/bond0/bonding/arp_ip_target
echo 1 > /sys/class/net/bond0/bonding/arp_validate


From 192.168.93.1 icmp_seq=132 Destination Host Unreachable
64 bytes from 192.168.93.2: icmp_seq=133 ttl=64 time=1243 ms
64 bytes from 192.168.93.2: icmp_seq=134 ttl=64 time=218 ms


 839  echo active > /sys/class/net/bond0/bonding/fail_over_mac
echo 100 > /sys/class/net/bond0/bonding/arp_interval
echo 192.168.93.2 > /sys/class/net/bond0/bonding/arp_ip_target
echo 1 > /sys/class/net/bond0/bonding/arp_validate
echo active > /sys/class/net/bond0/bonding/fail_over_mac
echo 100 > /sys/class/net/bond0/bonding/arp_interval
echo 192.168.93.2 > /sys/class/net/bond0/bonding/arp_ip_target
echo 1 > /sys/class/net/bond0/bonding/arp_validate



sudo modprobe bonding
echo bonding | sudo tee -a /etc/modules


network:
  version: 2
  renderer: networkd

  ethernets:
    ens18:
      addresses:
        - 192.168.122.15/24
      routes:
        - to: 0.0.0.0/0
          via: 192.168.122.1
      nameservers:
        addresses:
          - 8.8.8.8
          - 1.1.1.1
    ens19: {}
    ens20: {}
  bonds:
    bond0:
      interfaces: [ens19, ens20]
      addresses: [192.168.93.2/24]
      parameters:
        mode: 802.3ad 
        lacp-rate: fast
        mii-monitor-interval: 100
        transmit-hash-policy: layer2+3

