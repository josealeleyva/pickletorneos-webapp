# 🏓 Integración API DUPR (Pickleball Tournaments)

## 📌 Objetivo

Integrar DUPR (Dynamic Universal Pickleball Rating) en el sistema de gestión de torneos para:

- Vincular usuarios con su cuenta DUPR
- Obtener ratings oficiales
- Registrar partidos
- Enviar resultados a DUPR para cálculo de ranking

---

## 🔐 1. Autenticación

### Endpoint
POST /api/auth/v1.0/token

### Headers
x-authorization: base64(ClientKey:ClientSecret)  
accept: application/json  

### Ejemplo
curl -X POST https://uat.mydupr.com/api/auth/v1.0/token \
  -H "x-authorization: BASE64" \
  -H "accept: application/json"

### Resultado
Devuelve un `access_token` para usar en el resto de endpoints.

---

## 👤 2. Login con DUPR (SSO) — OBLIGATORIO

### ⚠️ Reglas importantes
- ❌ No permitir ingreso manual de DUPR ID
- ✅ Usar SSO oficial de DUPR
- ❌ No permitir cambiar DUPR ID manualmente
- ✅ Solo actualizar mediante re-login SSO

### Flujo
1. Usuario hace login con DUPR
2. DUPR devuelve datos del usuario
3. Guardar `dupr_id` en tu sistema

---

## 👥 3. Gestión de Jugadores

### Funcionalidades requeridas
- Buscar jugador por DUPR ID
- Obtener rating:
  - Singles
  - Doubles
- Vincular usuario local con DUPR

---

## 🎾 4. Gestión de Partidos (CORE)

### ⚠️ Punto más importante de toda la integración

Tu sistema DEBE:

- Crear partidos en DUPR
- Actualizar partidos
- Eliminar partidos
- Enviar resultados finales

### Flujo típico
1. Se crea partido en tu sistema
2. Se juega el partido
3. Se registra resultado
4. Se envía a DUPR
5. DUPR recalcula ranking

---

## 🏆 5. Torneos y Eventos

Si tu sistema maneja torneos:

### Debe soportar:
- Creación de eventos
- Restricciones de acceso

### Tipos de validación:
- DUPR normal
- DUPR+
- DUPR Verified

---

## ⭐ 6. Validación de membresías

### Validar:
- PREMIUM_L1 → DUPR+
- VERIFIED_L1 → Usuario verificado

### Requisito UX:
Mostrar modal si el usuario no cumple condiciones

---

## 🏢 7. Integración con Clubes (Opcional)

Si usás clubes:

### Debés validar:
- Membresía del usuario
- Rol:
  - DIRECTOR
  - ORGANIZER

### Reglas:
- Solo usuarios autorizados pueden gestionar clubes
- Validación vía API

---

## 💳 8. Pagos (Opcional)

Si hay torneos pagos:

- Simular pagos en entorno UAT
- Validar flujo completo

---

## 🔔 9. Webhooks (Recomendado)

### Usar para:
- Actualización de ratings
- Eventos de usuario

### Beneficio:
Evitar polling constante

---

## 🧪 10. Entorno UAT (Testing)

### Registro:
https://uat.dupr.gg/signup

### Notas:
- No hay verificación por email
- Se pueden crear usuarios de prueba
- Se pueden solicitar clubes de prueba

---

## 🛠 11. Soporte

- Revisar documentación primero
- Contacto: tech@mydupr.com
- Incluir siempre:
  - Client ID

⚠️ No dan soporte sobre tu código interno

---

## ✅ 12. Checklist de Aprobación

Antes de pasar a producción:

- [ ] Login con SSO implementado
- [ ] DUPR ID no editable manualmente
- [ ] Creación de partidos funcionando
- [ ] Envío de resultados correcto
- [ ] Manejo de torneos implementado
- [ ] Validación DUPR+ / Verified
- [ ] (Opcional) Clubes integrados
- [ ] (Opcional) Pagos simulados

---

## 🚀 13. Flujo Completo de Integración

1. Usuario inicia sesión con DUPR (SSO)
2. Se vincula dupr_id
3. Se registra a torneo
4. Se generan partidos
5. Se juegan partidos
6. Se envían resultados a DUPR
7. DUPR recalcula ranking
8. (Opcional) webhook notifica cambios

---

## 🧠 Notas Clave

- DUPR NO es solo lectura
- Tu sistema es responsable de los resultados
- La integración es bidireccional
- SSO es obligatorio
- Sin cumplir requisitos → no hay acceso a producción

---

## 📚 Documentación oficial

- https://dupr.gitbook.io/dupr-raas
- https://uat.mydupr.com/api/v3/api-docs
