FROM nginx:1.27-alpine

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/
COPY templates /etc/nginx/templates/

# Définir l'upstream pour PHP
RUN echo "upstream php-upstream { server bookmycut_php:9003; }" > /etc/nginx/conf.d/upstream.conf

# Exposer les ports nécessaires
EXPOSE 80
EXPOSE 443
