FROM anythink1/esd:latest
MAINTAINER ESD

COPY ./ /data

EXPOSE 80
WORKDIR /data

ENTRYPOINT ["/usr/local/bin/php", "start_server.php", "start"]