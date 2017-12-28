FROM nginx:alpine

RUN deluser nginx

RUN addgroup -g 1000 nginx && \
    adduser -D -u 1000 -G nginx nginx