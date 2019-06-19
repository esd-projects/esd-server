FROM anythink1/esd:latest
MAINTAINER anythink

ENV ESD_ENV deploy
COPY ./ /data

EXPOSE 8080
WORKDIR /data

ENTRYPOINT ["/usr/local/bin/php", "start_server.php", "start"]