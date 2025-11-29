sudo sysctl -w net.ipv4.ip_forward=1
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf

sudo iptables -A FORWARD -s 10.0.6.2/32 -j ACCEPT
sudo iptables -A FORWARD -d 10.0.6.2/32 -j ACCEPT

sudo iptables -A FORWARD -s 10.0.1.0/24 -d 10.0.5.0/24 -j ACCEPT
sudo iptables -A FORWARD -s 10.0.5.0/24 -d 10.0.1.0/24 -j ACCEPT


sudo iptables -A FORWARD -s 10.0.2.0/24 -d 10.0.4.0/24 -j ACCEPT
sudo iptables -A FORWARD -s 10.0.4.0/24 -d 10.0.2.0/24 -j ACCEPT


sudo iptables -A FORWARD -m state --state ESTABLISHED,RELATED -j ACCEPT
    
sudo iptables -A FORWARD -j DROP

Сохраняем правила (чтобы не исчезли после перезагрузки)
Ubuntu/Debian:

Установить пакет:

sudo apt install iptables-persistent

Сохранить:

sudo netfilter-persistent save

ip addr add 10.0.7.2/24 dev ens19

ip link set dev ens19 up