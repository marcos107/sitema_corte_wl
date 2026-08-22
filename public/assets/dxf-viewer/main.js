var Wh = Object.defineProperty;
var ba = (a) => {
  throw TypeError(a);
};
var Ih = (a, t, e) => t in a ? Wh(a, t, { enumerable: !0, configurable: !0, writable: !0, value: e }) : a[t] = e;
var Qt = (a, t, e) => Ih(a, typeof t != "symbol" ? t + "" : t, e), ya = (a, t, e) => t.has(a) || ba("Cannot " + e);
var Ys = (a, t, e) => (ya(a, t, "read from private field"), e ? e.call(a) : t.get(a)), Ps = (a, t, e) => t.has(a) ? ba("Cannot add the same private member more than once") : t instanceof WeakSet ? t.add(a) : t.set(a, e), As = (a, t, e, s) => (ya(a, t, "write to private field"), s ? s.call(a, e) : t.set(a, e), e);
/**
 * @license
 * Copyright 2010-2024 Three.js Authors
 * SPDX-License-Identifier: MIT
 */
const xc = "171";
const gc = "", yt = "srgb", xa = "srgb-linear", ga = "linear", Ks = "srgb";
class Fs {
  addEventListener(t, e) {
    this._listeners === void 0 && (this._listeners = {});
    const s = this._listeners;
    s[t] === void 0 && (s[t] = []), s[t].indexOf(e) === -1 && s[t].push(e);
  }
  hasEventListener(t, e) {
    if (this._listeners === void 0) return !1;
    const s = this._listeners;
    return s[t] !== void 0 && s[t].indexOf(e) !== -1;
  }
  removeEventListener(t, e) {
    if (this._listeners === void 0) return;
    const i = this._listeners[t];
    if (i !== void 0) {
      const n = i.indexOf(e);
      n !== -1 && i.splice(n, 1);
    }
  }
  dispatchEvent(t) {
    if (this._listeners === void 0) return;
    const s = this._listeners[t.type];
    if (s !== void 0) {
      t.target = this;
      const i = s.slice(0);
      for (let n = 0, r = i.length; n < r; n++)
        i[n].call(this, t);
      t.target = null;
    }
  }
}
const Q = ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "0a", "0b", "0c", "0d", "0e", "0f", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "1a", "1b", "1c", "1d", "1e", "1f", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "2a", "2b", "2c", "2d", "2e", "2f", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "3a", "3b", "3c", "3d", "3e", "3f", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "4a", "4b", "4c", "4d", "4e", "4f", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59", "5a", "5b", "5c", "5d", "5e", "5f", "60", "61", "62", "63", "64", "65", "66", "67", "68", "69", "6a", "6b", "6c", "6d", "6e", "6f", "70", "71", "72", "73", "74", "75", "76", "77", "78", "79", "7a", "7b", "7c", "7d", "7e", "7f", "80", "81", "82", "83", "84", "85", "86", "87", "88", "89", "8a", "8b", "8c", "8d", "8e", "8f", "90", "91", "92", "93", "94", "95", "96", "97", "98", "99", "9a", "9b", "9c", "9d", "9e", "9f", "a0", "a1", "a2", "a3", "a4", "a5", "a6", "a7", "a8", "a9", "aa", "ab", "ac", "ad", "ae", "af", "b0", "b1", "b2", "b3", "b4", "b5", "b6", "b7", "b8", "b9", "ba", "bb", "bc", "bd", "be", "bf", "c0", "c1", "c2", "c3", "c4", "c5", "c6", "c7", "c8", "c9", "ca", "cb", "cc", "cd", "ce", "cf", "d0", "d1", "d2", "d3", "d4", "d5", "d6", "d7", "d8", "d9", "da", "db", "dc", "dd", "de", "df", "e0", "e1", "e2", "e3", "e4", "e5", "e6", "e7", "e8", "e9", "ea", "eb", "ec", "ed", "ee", "ef", "f0", "f1", "f2", "f3", "f4", "f5", "f6", "f7", "f8", "f9", "fa", "fb", "fc", "fd", "fe", "ff"];
function Ve() {
  const a = Math.random() * 4294967295 | 0, t = Math.random() * 4294967295 | 0, e = Math.random() * 4294967295 | 0, s = Math.random() * 4294967295 | 0;
  return (Q[a & 255] + Q[a >> 8 & 255] + Q[a >> 16 & 255] + Q[a >> 24 & 255] + "-" + Q[t & 255] + Q[t >> 8 & 255] + "-" + Q[t >> 16 & 15 | 64] + Q[t >> 24 & 255] + "-" + Q[e & 63 | 128] + Q[e >> 8 & 255] + "-" + Q[e >> 16 & 255] + Q[e >> 24 & 255] + Q[s & 255] + Q[s >> 8 & 255] + Q[s >> 16 & 255] + Q[s >> 24 & 255]).toLowerCase();
}
function T(a, t, e) {
  return Math.max(t, Math.min(e, a));
}
function Ch(a, t) {
  return (a % t + t) % t;
}
function Js(a, t, e) {
  return (1 - e) * a + e * t;
}
function Le(a, t) {
  switch (t.constructor) {
    case Float32Array:
      return a;
    case Uint32Array:
      return a / 4294967295;
    case Uint16Array:
      return a / 65535;
    case Uint8Array:
      return a / 255;
    case Int32Array:
      return Math.max(a / 2147483647, -1);
    case Int16Array:
      return Math.max(a / 32767, -1);
    case Int8Array:
      return Math.max(a / 127, -1);
    default:
      throw new Error("Invalid component type.");
  }
}
function it(a, t) {
  switch (t.constructor) {
    case Float32Array:
      return a;
    case Uint32Array:
      return Math.round(a * 4294967295);
    case Uint16Array:
      return Math.round(a * 65535);
    case Uint8Array:
      return Math.round(a * 255);
    case Int32Array:
      return Math.round(a * 2147483647);
    case Int16Array:
      return Math.round(a * 32767);
    case Int8Array:
      return Math.round(a * 127);
    default:
      throw new Error("Invalid component type.");
  }
}
class v {
  constructor(t = 0, e = 0) {
    v.prototype.isVector2 = !0, this.x = t, this.y = e;
  }
  get width() {
    return this.x;
  }
  set width(t) {
    this.x = t;
  }
  get height() {
    return this.y;
  }
  set height(t) {
    this.y = t;
  }
  set(t, e) {
    return this.x = t, this.y = e, this;
  }
  setScalar(t) {
    return this.x = t, this.y = t, this;
  }
  setX(t) {
    return this.x = t, this;
  }
  setY(t) {
    return this.y = t, this;
  }
  setComponent(t, e) {
    switch (t) {
      case 0:
        this.x = e;
        break;
      case 1:
        this.y = e;
        break;
      default:
        throw new Error("index is out of range: " + t);
    }
    return this;
  }
  getComponent(t) {
    switch (t) {
      case 0:
        return this.x;
      case 1:
        return this.y;
      default:
        throw new Error("index is out of range: " + t);
    }
  }
  clone() {
    return new this.constructor(this.x, this.y);
  }
  copy(t) {
    return this.x = t.x, this.y = t.y, this;
  }
  add(t) {
    return this.x += t.x, this.y += t.y, this;
  }
  addScalar(t) {
    return this.x += t, this.y += t, this;
  }
  addVectors(t, e) {
    return this.x = t.x + e.x, this.y = t.y + e.y, this;
  }
  addScaledVector(t, e) {
    return this.x += t.x * e, this.y += t.y * e, this;
  }
  sub(t) {
    return this.x -= t.x, this.y -= t.y, this;
  }
  subScalar(t) {
    return this.x -= t, this.y -= t, this;
  }
  subVectors(t, e) {
    return this.x = t.x - e.x, this.y = t.y - e.y, this;
  }
  multiply(t) {
    return this.x *= t.x, this.y *= t.y, this;
  }
  multiplyScalar(t) {
    return this.x *= t, this.y *= t, this;
  }
  divide(t) {
    return this.x /= t.x, this.y /= t.y, this;
  }
  divideScalar(t) {
    return this.multiplyScalar(1 / t);
  }
  applyMatrix3(t) {
    const e = this.x, s = this.y, i = t.elements;
    return this.x = i[0] * e + i[3] * s + i[6], this.y = i[1] * e + i[4] * s + i[7], this;
  }
  min(t) {
    return this.x = Math.min(this.x, t.x), this.y = Math.min(this.y, t.y), this;
  }
  max(t) {
    return this.x = Math.max(this.x, t.x), this.y = Math.max(this.y, t.y), this;
  }
  clamp(t, e) {
    return this.x = T(this.x, t.x, e.x), this.y = T(this.y, t.y, e.y), this;
  }
  clampScalar(t, e) {
    return this.x = T(this.x, t, e), this.y = T(this.y, t, e), this;
  }
  clampLength(t, e) {
    const s = this.length();
    return this.divideScalar(s || 1).multiplyScalar(T(s, t, e));
  }
  floor() {
    return this.x = Math.floor(this.x), this.y = Math.floor(this.y), this;
  }
  ceil() {
    return this.x = Math.ceil(this.x), this.y = Math.ceil(this.y), this;
  }
  round() {
    return this.x = Math.round(this.x), this.y = Math.round(this.y), this;
  }
  roundToZero() {
    return this.x = Math.trunc(this.x), this.y = Math.trunc(this.y), this;
  }
  negate() {
    return this.x = -this.x, this.y = -this.y, this;
  }
  dot(t) {
    return this.x * t.x + this.y * t.y;
  }
  cross(t) {
    return this.x * t.y - this.y * t.x;
  }
  lengthSq() {
    return this.x * this.x + this.y * this.y;
  }
  length() {
    return Math.sqrt(this.x * this.x + this.y * this.y);
  }
  manhattanLength() {
    return Math.abs(this.x) + Math.abs(this.y);
  }
  normalize() {
    return this.divideScalar(this.length() || 1);
  }
  angle() {
    return Math.atan2(-this.y, -this.x) + Math.PI;
  }
  angleTo(t) {
    const e = Math.sqrt(this.lengthSq() * t.lengthSq());
    if (e === 0) return Math.PI / 2;
    const s = this.dot(t) / e;
    return Math.acos(T(s, -1, 1));
  }
  distanceTo(t) {
    return Math.sqrt(this.distanceToSquared(t));
  }
  distanceToSquared(t) {
    const e = this.x - t.x, s = this.y - t.y;
    return e * e + s * s;
  }
  manhattanDistanceTo(t) {
    return Math.abs(this.x - t.x) + Math.abs(this.y - t.y);
  }
  setLength(t) {
    return this.normalize().multiplyScalar(t);
  }
  lerp(t, e) {
    return this.x += (t.x - this.x) * e, this.y += (t.y - this.y) * e, this;
  }
  lerpVectors(t, e, s) {
    return this.x = t.x + (e.x - t.x) * s, this.y = t.y + (e.y - t.y) * s, this;
  }
  equals(t) {
    return t.x === this.x && t.y === this.y;
  }
  fromArray(t, e = 0) {
    return this.x = t[e], this.y = t[e + 1], this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this.x, t[e + 1] = this.y, t;
  }
  fromBufferAttribute(t, e) {
    return this.x = t.getX(e), this.y = t.getY(e), this;
  }
  rotateAround(t, e) {
    const s = Math.cos(e), i = Math.sin(e), n = this.x - t.x, r = this.y - t.y;
    return this.x = n * s - r * i + t.x, this.y = n * i + r * s + t.y, this;
  }
  random() {
    return this.x = Math.random(), this.y = Math.random(), this;
  }
  *[Symbol.iterator]() {
    yield this.x, yield this.y;
  }
}
class Ct {
  constructor(t, e, s, i, n, r, o, l, c) {
    Ct.prototype.isMatrix3 = !0, this.elements = [
      1,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      1
    ], t !== void 0 && this.set(t, e, s, i, n, r, o, l, c);
  }
  set(t, e, s, i, n, r, o, l, c) {
    const h = this.elements;
    return h[0] = t, h[1] = i, h[2] = o, h[3] = e, h[4] = n, h[5] = l, h[6] = s, h[7] = r, h[8] = c, this;
  }
  identity() {
    return this.set(
      1,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      1
    ), this;
  }
  copy(t) {
    const e = this.elements, s = t.elements;
    return e[0] = s[0], e[1] = s[1], e[2] = s[2], e[3] = s[3], e[4] = s[4], e[5] = s[5], e[6] = s[6], e[7] = s[7], e[8] = s[8], this;
  }
  extractBasis(t, e, s) {
    return t.setFromMatrix3Column(this, 0), e.setFromMatrix3Column(this, 1), s.setFromMatrix3Column(this, 2), this;
  }
  setFromMatrix4(t) {
    const e = t.elements;
    return this.set(
      e[0],
      e[4],
      e[8],
      e[1],
      e[5],
      e[9],
      e[2],
      e[6],
      e[10]
    ), this;
  }
  multiply(t) {
    return this.multiplyMatrices(this, t);
  }
  premultiply(t) {
    return this.multiplyMatrices(t, this);
  }
  multiplyMatrices(t, e) {
    const s = t.elements, i = e.elements, n = this.elements, r = s[0], o = s[3], l = s[6], c = s[1], h = s[4], u = s[7], d = s[2], p = s[5], m = s[8], f = i[0], b = i[3], y = i[6], x = i[1], g = i[4], S = i[7], Z = i[2], V = i[5], G = i[8];
    return n[0] = r * f + o * x + l * Z, n[3] = r * b + o * g + l * V, n[6] = r * y + o * S + l * G, n[1] = c * f + h * x + u * Z, n[4] = c * b + h * g + u * V, n[7] = c * y + h * S + u * G, n[2] = d * f + p * x + m * Z, n[5] = d * b + p * g + m * V, n[8] = d * y + p * S + m * G, this;
  }
  multiplyScalar(t) {
    const e = this.elements;
    return e[0] *= t, e[3] *= t, e[6] *= t, e[1] *= t, e[4] *= t, e[7] *= t, e[2] *= t, e[5] *= t, e[8] *= t, this;
  }
  determinant() {
    const t = this.elements, e = t[0], s = t[1], i = t[2], n = t[3], r = t[4], o = t[5], l = t[6], c = t[7], h = t[8];
    return e * r * h - e * o * c - s * n * h + s * o * l + i * n * c - i * r * l;
  }
  invert() {
    const t = this.elements, e = t[0], s = t[1], i = t[2], n = t[3], r = t[4], o = t[5], l = t[6], c = t[7], h = t[8], u = h * r - o * c, d = o * l - h * n, p = c * n - r * l, m = e * u + s * d + i * p;
    if (m === 0) return this.set(0, 0, 0, 0, 0, 0, 0, 0, 0);
    const f = 1 / m;
    return t[0] = u * f, t[1] = (i * c - h * s) * f, t[2] = (o * s - i * r) * f, t[3] = d * f, t[4] = (h * e - i * l) * f, t[5] = (i * n - o * e) * f, t[6] = p * f, t[7] = (s * l - c * e) * f, t[8] = (r * e - s * n) * f, this;
  }
  transpose() {
    let t;
    const e = this.elements;
    return t = e[1], e[1] = e[3], e[3] = t, t = e[2], e[2] = e[6], e[6] = t, t = e[5], e[5] = e[7], e[7] = t, this;
  }
  getNormalMatrix(t) {
    return this.setFromMatrix4(t).invert().transpose();
  }
  transposeIntoArray(t) {
    const e = this.elements;
    return t[0] = e[0], t[1] = e[3], t[2] = e[6], t[3] = e[1], t[4] = e[4], t[5] = e[7], t[6] = e[2], t[7] = e[5], t[8] = e[8], this;
  }
  setUvTransform(t, e, s, i, n, r, o) {
    const l = Math.cos(n), c = Math.sin(n);
    return this.set(
      s * l,
      s * c,
      -s * (l * r + c * o) + r + t,
      -i * c,
      i * l,
      -i * (-c * r + l * o) + o + e,
      0,
      0,
      1
    ), this;
  }
  //
  scale(t, e) {
    return this.premultiply(Ds.makeScale(t, e)), this;
  }
  rotate(t) {
    return this.premultiply(Ds.makeRotation(-t)), this;
  }
  translate(t, e) {
    return this.premultiply(Ds.makeTranslation(t, e)), this;
  }
  // for 2D Transforms
  makeTranslation(t, e) {
    return t.isVector2 ? this.set(
      1,
      0,
      t.x,
      0,
      1,
      t.y,
      0,
      0,
      1
    ) : this.set(
      1,
      0,
      t,
      0,
      1,
      e,
      0,
      0,
      1
    ), this;
  }
  makeRotation(t) {
    const e = Math.cos(t), s = Math.sin(t);
    return this.set(
      e,
      -s,
      0,
      s,
      e,
      0,
      0,
      0,
      1
    ), this;
  }
  makeScale(t, e) {
    return this.set(
      t,
      0,
      0,
      0,
      e,
      0,
      0,
      0,
      1
    ), this;
  }
  //
  equals(t) {
    const e = this.elements, s = t.elements;
    for (let i = 0; i < 9; i++)
      if (e[i] !== s[i]) return !1;
    return !0;
  }
  fromArray(t, e = 0) {
    for (let s = 0; s < 9; s++)
      this.elements[s] = t[s + e];
    return this;
  }
  toArray(t = [], e = 0) {
    const s = this.elements;
    return t[e] = s[0], t[e + 1] = s[1], t[e + 2] = s[2], t[e + 3] = s[3], t[e + 4] = s[4], t[e + 5] = s[5], t[e + 6] = s[6], t[e + 7] = s[7], t[e + 8] = s[8], t;
  }
  clone() {
    return new this.constructor().fromArray(this.elements);
  }
}
const Ds = /* @__PURE__ */ new Ct();
function Eh(a) {
  for (let t = a.length - 1; t >= 0; --t)
    if (a[t] >= 65535) return !0;
  return !1;
}
function Ra(a) {
  return document.createElementNS("http://www.w3.org/1999/xhtml", a);
}
const Sa = /* @__PURE__ */ new Ct().set(
  0.4123908,
  0.3575843,
  0.1804808,
  0.212639,
  0.7151687,
  0.0721923,
  0.0193308,
  0.1191948,
  0.9505322
), Za = /* @__PURE__ */ new Ct().set(
  3.2409699,
  -1.5373832,
  -0.4986108,
  -0.9692436,
  1.8759675,
  0.0415551,
  0.0556301,
  -0.203977,
  1.0569715
);
function Bh() {
  const a = {
    enabled: !0,
    workingColorSpace: xa,
    /**
     * Implementations of supported color spaces.
     *
     * Required:
     *	- primaries: chromaticity coordinates [ rx ry gx gy bx by ]
     *	- whitePoint: reference white [ x y ]
     *	- transfer: transfer function (pre-defined)
     *	- toXYZ: Matrix3 RGB to XYZ transform
     *	- fromXYZ: Matrix3 XYZ to RGB transform
     *	- luminanceCoefficients: RGB luminance coefficients
     *
     * Optional:
     *  - outputColorSpaceConfig: { drawingBufferColorSpace: ColorSpace }
     *  - workingColorSpaceConfig: { unpackColorSpace: ColorSpace }
     *
     * Reference:
     * - https://www.russellcottrell.com/photo/matrixCalculator.htm
     */
    spaces: {},
    convert: function(i, n, r) {
      return this.enabled === !1 || n === r || !n || !r || (this.spaces[n].transfer === Ks && (i.r = Wt(i.r), i.g = Wt(i.g), i.b = Wt(i.b)), this.spaces[n].primaries !== this.spaces[r].primaries && (i.applyMatrix3(this.spaces[n].toXYZ), i.applyMatrix3(this.spaces[r].fromXYZ)), this.spaces[r].transfer === Ks && (i.r = Ze(i.r), i.g = Ze(i.g), i.b = Ze(i.b))), i;
    },
    fromWorkingColorSpace: function(i, n) {
      return this.convert(i, this.workingColorSpace, n);
    },
    toWorkingColorSpace: function(i, n) {
      return this.convert(i, n, this.workingColorSpace);
    },
    getPrimaries: function(i) {
      return this.spaces[i].primaries;
    },
    getTransfer: function(i) {
      return i === gc ? ga : this.spaces[i].transfer;
    },
    getLuminanceCoefficients: function(i, n = this.workingColorSpace) {
      return i.fromArray(this.spaces[n].luminanceCoefficients);
    },
    define: function(i) {
      Object.assign(this.spaces, i);
    },
    // Internal APIs
    _getMatrix: function(i, n, r) {
      return i.copy(this.spaces[n].toXYZ).multiply(this.spaces[r].fromXYZ);
    },
    _getDrawingBufferColorSpace: function(i) {
      return this.spaces[i].outputColorSpaceConfig.drawingBufferColorSpace;
    },
    _getUnpackColorSpace: function(i = this.workingColorSpace) {
      return this.spaces[i].workingColorSpaceConfig.unpackColorSpace;
    }
  }, t = [0.64, 0.33, 0.3, 0.6, 0.15, 0.06], e = [0.2126, 0.7152, 0.0722], s = [0.3127, 0.329];
  return a.define({
    [xa]: {
      primaries: t,
      whitePoint: s,
      transfer: ga,
      toXYZ: Sa,
      fromXYZ: Za,
      luminanceCoefficients: e,
      workingColorSpaceConfig: { unpackColorSpace: yt },
      outputColorSpaceConfig: { drawingBufferColorSpace: yt }
    },
    [yt]: {
      primaries: t,
      whitePoint: s,
      transfer: Ks,
      toXYZ: Sa,
      fromXYZ: Za,
      luminanceCoefficients: e,
      outputColorSpaceConfig: { drawingBufferColorSpace: yt }
    }
  }), a;
}
const pt = /* @__PURE__ */ Bh();
function Wt(a) {
  return a < 0.04045 ? a * 0.0773993808 : Math.pow(a * 0.9478672986 + 0.0521327014, 2.4);
}
function Ze(a) {
  return a < 31308e-7 ? a * 12.92 : 1.055 * Math.pow(a, 0.41666) - 0.055;
}
let ce;
class Uh {
  static getDataURL(t) {
    if (/^data:/i.test(t.src) || typeof HTMLCanvasElement > "u")
      return t.src;
    let e;
    if (t instanceof HTMLCanvasElement)
      e = t;
    else {
      ce === void 0 && (ce = Ra("canvas")), ce.width = t.width, ce.height = t.height;
      const s = ce.getContext("2d");
      t instanceof ImageData ? s.putImageData(t, 0, 0) : s.drawImage(t, 0, 0, t.width, t.height), e = ce;
    }
    return e.width > 2048 || e.height > 2048 ? (console.warn("THREE.ImageUtils.getDataURL: Image converted to jpg for performance reasons", t), e.toDataURL("image/jpeg", 0.6)) : e.toDataURL("image/png");
  }
  static sRGBToLinear(t) {
    if (typeof HTMLImageElement < "u" && t instanceof HTMLImageElement || typeof HTMLCanvasElement < "u" && t instanceof HTMLCanvasElement || typeof ImageBitmap < "u" && t instanceof ImageBitmap) {
      const e = Ra("canvas");
      e.width = t.width, e.height = t.height;
      const s = e.getContext("2d");
      s.drawImage(t, 0, 0, t.width, t.height);
      const i = s.getImageData(0, 0, t.width, t.height), n = i.data;
      for (let r = 0; r < n.length; r++)
        n[r] = Wt(n[r] / 255) * 255;
      return s.putImageData(i, 0, 0), e;
    } else if (t.data) {
      const e = t.data.slice(0);
      for (let s = 0; s < e.length; s++)
        e instanceof Uint8Array || e instanceof Uint8ClampedArray ? e[s] = Math.floor(Wt(e[s] / 255) * 255) : e[s] = Wt(e[s]);
      return {
        data: e,
        width: t.width,
        height: t.height
      };
    } else
      return console.warn("THREE.ImageUtils.sRGBToLinear(): Unsupported image type. No color space conversion applied."), t;
  }
}
let Nh = 0;
class _h {
  constructor(t = null) {
    this.isSource = !0, Object.defineProperty(this, "id", { value: Nh++ }), this.uuid = Ve(), this.data = t, this.dataReady = !0, this.version = 0;
  }
  set needsUpdate(t) {
    t === !0 && this.version++;
  }
  toJSON(t) {
    const e = t === void 0 || typeof t == "string";
    if (!e && t.images[this.uuid] !== void 0)
      return t.images[this.uuid];
    const s = {
      uuid: this.uuid,
      url: ""
    }, i = this.data;
    if (i !== null) {
      let n;
      if (Array.isArray(i)) {
        n = [];
        for (let r = 0, o = i.length; r < o; r++)
          i[r].isDataTexture ? n.push(Qs(i[r].image)) : n.push(Qs(i[r]));
      } else
        n = Qs(i);
      s.url = n;
    }
    return e || (t.images[this.uuid] = s), s;
  }
}
function Qs(a) {
  return typeof HTMLImageElement < "u" && a instanceof HTMLImageElement || typeof HTMLCanvasElement < "u" && a instanceof HTMLCanvasElement || typeof ImageBitmap < "u" && a instanceof ImageBitmap ? Uh.getDataURL(a) : a.data ? {
    data: Array.from(a.data),
    width: a.width,
    height: a.height,
    type: a.data.constructor.name
  } : (console.warn("THREE.Texture: Unable to serialize Texture."), {});
}
let Hh = 0;
class It extends Fs {
  constructor(t = It.DEFAULT_IMAGE, e = It.DEFAULT_MAPPING, s = 1001, i = 1001, n = 1006, r = 1008, o = 1023, l = 1009, c = It.DEFAULT_ANISOTROPY, h = gc) {
    super(), this.isTexture = !0, Object.defineProperty(this, "id", { value: Hh++ }), this.uuid = Ve(), this.name = "", this.source = new _h(t), this.mipmaps = [], this.mapping = e, this.channel = 0, this.wrapS = s, this.wrapT = i, this.magFilter = n, this.minFilter = r, this.anisotropy = c, this.format = o, this.internalFormat = null, this.type = l, this.offset = new v(0, 0), this.repeat = new v(1, 1), this.center = new v(0, 0), this.rotation = 0, this.matrixAutoUpdate = !0, this.matrix = new Ct(), this.generateMipmaps = !0, this.premultiplyAlpha = !1, this.flipY = !0, this.unpackAlignment = 4, this.colorSpace = h, this.userData = {}, this.version = 0, this.onUpdate = null, this.isRenderTargetTexture = !1, this.pmremVersion = 0;
  }
  get image() {
    return this.source.data;
  }
  set image(t = null) {
    this.source.data = t;
  }
  updateMatrix() {
    this.matrix.setUvTransform(this.offset.x, this.offset.y, this.repeat.x, this.repeat.y, this.rotation, this.center.x, this.center.y);
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    return this.name = t.name, this.source = t.source, this.mipmaps = t.mipmaps.slice(0), this.mapping = t.mapping, this.channel = t.channel, this.wrapS = t.wrapS, this.wrapT = t.wrapT, this.magFilter = t.magFilter, this.minFilter = t.minFilter, this.anisotropy = t.anisotropy, this.format = t.format, this.internalFormat = t.internalFormat, this.type = t.type, this.offset.copy(t.offset), this.repeat.copy(t.repeat), this.center.copy(t.center), this.rotation = t.rotation, this.matrixAutoUpdate = t.matrixAutoUpdate, this.matrix.copy(t.matrix), this.generateMipmaps = t.generateMipmaps, this.premultiplyAlpha = t.premultiplyAlpha, this.flipY = t.flipY, this.unpackAlignment = t.unpackAlignment, this.colorSpace = t.colorSpace, this.userData = JSON.parse(JSON.stringify(t.userData)), this.needsUpdate = !0, this;
  }
  toJSON(t) {
    const e = t === void 0 || typeof t == "string";
    if (!e && t.textures[this.uuid] !== void 0)
      return t.textures[this.uuid];
    const s = {
      metadata: {
        version: 4.6,
        type: "Texture",
        generator: "Texture.toJSON"
      },
      uuid: this.uuid,
      name: this.name,
      image: this.source.toJSON(t).uuid,
      mapping: this.mapping,
      channel: this.channel,
      repeat: [this.repeat.x, this.repeat.y],
      offset: [this.offset.x, this.offset.y],
      center: [this.center.x, this.center.y],
      rotation: this.rotation,
      wrap: [this.wrapS, this.wrapT],
      format: this.format,
      internalFormat: this.internalFormat,
      type: this.type,
      colorSpace: this.colorSpace,
      minFilter: this.minFilter,
      magFilter: this.magFilter,
      anisotropy: this.anisotropy,
      flipY: this.flipY,
      generateMipmaps: this.generateMipmaps,
      premultiplyAlpha: this.premultiplyAlpha,
      unpackAlignment: this.unpackAlignment
    };
    return Object.keys(this.userData).length > 0 && (s.userData = this.userData), e || (t.textures[this.uuid] = s), s;
  }
  dispose() {
    this.dispatchEvent({ type: "dispose" });
  }
  transformUv(t) {
    if (this.mapping !== 300) return t;
    if (t.applyMatrix3(this.matrix), t.x < 0 || t.x > 1)
      switch (this.wrapS) {
        case 1e3:
          t.x = t.x - Math.floor(t.x);
          break;
        case 1001:
          t.x = t.x < 0 ? 0 : 1;
          break;
        case 1002:
          Math.abs(Math.floor(t.x) % 2) === 1 ? t.x = Math.ceil(t.x) - t.x : t.x = t.x - Math.floor(t.x);
          break;
      }
    if (t.y < 0 || t.y > 1)
      switch (this.wrapT) {
        case 1e3:
          t.y = t.y - Math.floor(t.y);
          break;
        case 1001:
          t.y = t.y < 0 ? 0 : 1;
          break;
        case 1002:
          Math.abs(Math.floor(t.y) % 2) === 1 ? t.y = Math.ceil(t.y) - t.y : t.y = t.y - Math.floor(t.y);
          break;
      }
    return this.flipY && (t.y = 1 - t.y), t;
  }
  set needsUpdate(t) {
    t === !0 && (this.version++, this.source.needsUpdate = !0);
  }
  set needsPMREMUpdate(t) {
    t === !0 && this.pmremVersion++;
  }
}
It.DEFAULT_IMAGE = null;
It.DEFAULT_MAPPING = 300;
It.DEFAULT_ANISOTROPY = 1;
class Pe {
  constructor(t = 0, e = 0, s = 0, i = 1) {
    Pe.prototype.isVector4 = !0, this.x = t, this.y = e, this.z = s, this.w = i;
  }
  get width() {
    return this.z;
  }
  set width(t) {
    this.z = t;
  }
  get height() {
    return this.w;
  }
  set height(t) {
    this.w = t;
  }
  set(t, e, s, i) {
    return this.x = t, this.y = e, this.z = s, this.w = i, this;
  }
  setScalar(t) {
    return this.x = t, this.y = t, this.z = t, this.w = t, this;
  }
  setX(t) {
    return this.x = t, this;
  }
  setY(t) {
    return this.y = t, this;
  }
  setZ(t) {
    return this.z = t, this;
  }
  setW(t) {
    return this.w = t, this;
  }
  setComponent(t, e) {
    switch (t) {
      case 0:
        this.x = e;
        break;
      case 1:
        this.y = e;
        break;
      case 2:
        this.z = e;
        break;
      case 3:
        this.w = e;
        break;
      default:
        throw new Error("index is out of range: " + t);
    }
    return this;
  }
  getComponent(t) {
    switch (t) {
      case 0:
        return this.x;
      case 1:
        return this.y;
      case 2:
        return this.z;
      case 3:
        return this.w;
      default:
        throw new Error("index is out of range: " + t);
    }
  }
  clone() {
    return new this.constructor(this.x, this.y, this.z, this.w);
  }
  copy(t) {
    return this.x = t.x, this.y = t.y, this.z = t.z, this.w = t.w !== void 0 ? t.w : 1, this;
  }
  add(t) {
    return this.x += t.x, this.y += t.y, this.z += t.z, this.w += t.w, this;
  }
  addScalar(t) {
    return this.x += t, this.y += t, this.z += t, this.w += t, this;
  }
  addVectors(t, e) {
    return this.x = t.x + e.x, this.y = t.y + e.y, this.z = t.z + e.z, this.w = t.w + e.w, this;
  }
  addScaledVector(t, e) {
    return this.x += t.x * e, this.y += t.y * e, this.z += t.z * e, this.w += t.w * e, this;
  }
  sub(t) {
    return this.x -= t.x, this.y -= t.y, this.z -= t.z, this.w -= t.w, this;
  }
  subScalar(t) {
    return this.x -= t, this.y -= t, this.z -= t, this.w -= t, this;
  }
  subVectors(t, e) {
    return this.x = t.x - e.x, this.y = t.y - e.y, this.z = t.z - e.z, this.w = t.w - e.w, this;
  }
  multiply(t) {
    return this.x *= t.x, this.y *= t.y, this.z *= t.z, this.w *= t.w, this;
  }
  multiplyScalar(t) {
    return this.x *= t, this.y *= t, this.z *= t, this.w *= t, this;
  }
  applyMatrix4(t) {
    const e = this.x, s = this.y, i = this.z, n = this.w, r = t.elements;
    return this.x = r[0] * e + r[4] * s + r[8] * i + r[12] * n, this.y = r[1] * e + r[5] * s + r[9] * i + r[13] * n, this.z = r[2] * e + r[6] * s + r[10] * i + r[14] * n, this.w = r[3] * e + r[7] * s + r[11] * i + r[15] * n, this;
  }
  divide(t) {
    return this.x /= t.x, this.y /= t.y, this.z /= t.z, this.w /= t.w, this;
  }
  divideScalar(t) {
    return this.multiplyScalar(1 / t);
  }
  setAxisAngleFromQuaternion(t) {
    this.w = 2 * Math.acos(t.w);
    const e = Math.sqrt(1 - t.w * t.w);
    return e < 1e-4 ? (this.x = 1, this.y = 0, this.z = 0) : (this.x = t.x / e, this.y = t.y / e, this.z = t.z / e), this;
  }
  setAxisAngleFromRotationMatrix(t) {
    let e, s, i, n;
    const l = t.elements, c = l[0], h = l[4], u = l[8], d = l[1], p = l[5], m = l[9], f = l[2], b = l[6], y = l[10];
    if (Math.abs(h - d) < 0.01 && Math.abs(u - f) < 0.01 && Math.abs(m - b) < 0.01) {
      if (Math.abs(h + d) < 0.1 && Math.abs(u + f) < 0.1 && Math.abs(m + b) < 0.1 && Math.abs(c + p + y - 3) < 0.1)
        return this.set(1, 0, 0, 0), this;
      e = Math.PI;
      const g = (c + 1) / 2, S = (p + 1) / 2, Z = (y + 1) / 2, V = (h + d) / 4, G = (u + f) / 4, M = (m + b) / 4;
      return g > S && g > Z ? g < 0.01 ? (s = 0, i = 0.707106781, n = 0.707106781) : (s = Math.sqrt(g), i = V / s, n = G / s) : S > Z ? S < 0.01 ? (s = 0.707106781, i = 0, n = 0.707106781) : (i = Math.sqrt(S), s = V / i, n = M / i) : Z < 0.01 ? (s = 0.707106781, i = 0.707106781, n = 0) : (n = Math.sqrt(Z), s = G / n, i = M / n), this.set(s, i, n, e), this;
    }
    let x = Math.sqrt((b - m) * (b - m) + (u - f) * (u - f) + (d - h) * (d - h));
    return Math.abs(x) < 1e-3 && (x = 1), this.x = (b - m) / x, this.y = (u - f) / x, this.z = (d - h) / x, this.w = Math.acos((c + p + y - 1) / 2), this;
  }
  setFromMatrixPosition(t) {
    const e = t.elements;
    return this.x = e[12], this.y = e[13], this.z = e[14], this.w = e[15], this;
  }
  min(t) {
    return this.x = Math.min(this.x, t.x), this.y = Math.min(this.y, t.y), this.z = Math.min(this.z, t.z), this.w = Math.min(this.w, t.w), this;
  }
  max(t) {
    return this.x = Math.max(this.x, t.x), this.y = Math.max(this.y, t.y), this.z = Math.max(this.z, t.z), this.w = Math.max(this.w, t.w), this;
  }
  clamp(t, e) {
    return this.x = T(this.x, t.x, e.x), this.y = T(this.y, t.y, e.y), this.z = T(this.z, t.z, e.z), this.w = T(this.w, t.w, e.w), this;
  }
  clampScalar(t, e) {
    return this.x = T(this.x, t, e), this.y = T(this.y, t, e), this.z = T(this.z, t, e), this.w = T(this.w, t, e), this;
  }
  clampLength(t, e) {
    const s = this.length();
    return this.divideScalar(s || 1).multiplyScalar(T(s, t, e));
  }
  floor() {
    return this.x = Math.floor(this.x), this.y = Math.floor(this.y), this.z = Math.floor(this.z), this.w = Math.floor(this.w), this;
  }
  ceil() {
    return this.x = Math.ceil(this.x), this.y = Math.ceil(this.y), this.z = Math.ceil(this.z), this.w = Math.ceil(this.w), this;
  }
  round() {
    return this.x = Math.round(this.x), this.y = Math.round(this.y), this.z = Math.round(this.z), this.w = Math.round(this.w), this;
  }
  roundToZero() {
    return this.x = Math.trunc(this.x), this.y = Math.trunc(this.y), this.z = Math.trunc(this.z), this.w = Math.trunc(this.w), this;
  }
  negate() {
    return this.x = -this.x, this.y = -this.y, this.z = -this.z, this.w = -this.w, this;
  }
  dot(t) {
    return this.x * t.x + this.y * t.y + this.z * t.z + this.w * t.w;
  }
  lengthSq() {
    return this.x * this.x + this.y * this.y + this.z * this.z + this.w * this.w;
  }
  length() {
    return Math.sqrt(this.x * this.x + this.y * this.y + this.z * this.z + this.w * this.w);
  }
  manhattanLength() {
    return Math.abs(this.x) + Math.abs(this.y) + Math.abs(this.z) + Math.abs(this.w);
  }
  normalize() {
    return this.divideScalar(this.length() || 1);
  }
  setLength(t) {
    return this.normalize().multiplyScalar(t);
  }
  lerp(t, e) {
    return this.x += (t.x - this.x) * e, this.y += (t.y - this.y) * e, this.z += (t.z - this.z) * e, this.w += (t.w - this.w) * e, this;
  }
  lerpVectors(t, e, s) {
    return this.x = t.x + (e.x - t.x) * s, this.y = t.y + (e.y - t.y) * s, this.z = t.z + (e.z - t.z) * s, this.w = t.w + (e.w - t.w) * s, this;
  }
  equals(t) {
    return t.x === this.x && t.y === this.y && t.z === this.z && t.w === this.w;
  }
  fromArray(t, e = 0) {
    return this.x = t[e], this.y = t[e + 1], this.z = t[e + 2], this.w = t[e + 3], this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this.x, t[e + 1] = this.y, t[e + 2] = this.z, t[e + 3] = this.w, t;
  }
  fromBufferAttribute(t, e) {
    return this.x = t.getX(e), this.y = t.getY(e), this.z = t.getZ(e), this.w = t.getW(e), this;
  }
  random() {
    return this.x = Math.random(), this.y = Math.random(), this.z = Math.random(), this.w = Math.random(), this;
  }
  *[Symbol.iterator]() {
    yield this.x, yield this.y, yield this.z, yield this.w;
  }
}
let ee = class {
  constructor(t = 0, e = 0, s = 0, i = 1) {
    this.isQuaternion = !0, this._x = t, this._y = e, this._z = s, this._w = i;
  }
  static slerpFlat(t, e, s, i, n, r, o) {
    let l = s[i + 0], c = s[i + 1], h = s[i + 2], u = s[i + 3];
    const d = n[r + 0], p = n[r + 1], m = n[r + 2], f = n[r + 3];
    if (o === 0) {
      t[e + 0] = l, t[e + 1] = c, t[e + 2] = h, t[e + 3] = u;
      return;
    }
    if (o === 1) {
      t[e + 0] = d, t[e + 1] = p, t[e + 2] = m, t[e + 3] = f;
      return;
    }
    if (u !== f || l !== d || c !== p || h !== m) {
      let b = 1 - o;
      const y = l * d + c * p + h * m + u * f, x = y >= 0 ? 1 : -1, g = 1 - y * y;
      if (g > Number.EPSILON) {
        const Z = Math.sqrt(g), V = Math.atan2(Z, y * x);
        b = Math.sin(b * V) / Z, o = Math.sin(o * V) / Z;
      }
      const S = o * x;
      if (l = l * b + d * S, c = c * b + p * S, h = h * b + m * S, u = u * b + f * S, b === 1 - o) {
        const Z = 1 / Math.sqrt(l * l + c * c + h * h + u * u);
        l *= Z, c *= Z, h *= Z, u *= Z;
      }
    }
    t[e] = l, t[e + 1] = c, t[e + 2] = h, t[e + 3] = u;
  }
  static multiplyQuaternionsFlat(t, e, s, i, n, r) {
    const o = s[i], l = s[i + 1], c = s[i + 2], h = s[i + 3], u = n[r], d = n[r + 1], p = n[r + 2], m = n[r + 3];
    return t[e] = o * m + h * u + l * p - c * d, t[e + 1] = l * m + h * d + c * u - o * p, t[e + 2] = c * m + h * p + o * d - l * u, t[e + 3] = h * m - o * u - l * d - c * p, t;
  }
  get x() {
    return this._x;
  }
  set x(t) {
    this._x = t, this._onChangeCallback();
  }
  get y() {
    return this._y;
  }
  set y(t) {
    this._y = t, this._onChangeCallback();
  }
  get z() {
    return this._z;
  }
  set z(t) {
    this._z = t, this._onChangeCallback();
  }
  get w() {
    return this._w;
  }
  set w(t) {
    this._w = t, this._onChangeCallback();
  }
  set(t, e, s, i) {
    return this._x = t, this._y = e, this._z = s, this._w = i, this._onChangeCallback(), this;
  }
  clone() {
    return new this.constructor(this._x, this._y, this._z, this._w);
  }
  copy(t) {
    return this._x = t.x, this._y = t.y, this._z = t.z, this._w = t.w, this._onChangeCallback(), this;
  }
  setFromEuler(t, e = !0) {
    const s = t._x, i = t._y, n = t._z, r = t._order, o = Math.cos, l = Math.sin, c = o(s / 2), h = o(i / 2), u = o(n / 2), d = l(s / 2), p = l(i / 2), m = l(n / 2);
    switch (r) {
      case "XYZ":
        this._x = d * h * u + c * p * m, this._y = c * p * u - d * h * m, this._z = c * h * m + d * p * u, this._w = c * h * u - d * p * m;
        break;
      case "YXZ":
        this._x = d * h * u + c * p * m, this._y = c * p * u - d * h * m, this._z = c * h * m - d * p * u, this._w = c * h * u + d * p * m;
        break;
      case "ZXY":
        this._x = d * h * u - c * p * m, this._y = c * p * u + d * h * m, this._z = c * h * m + d * p * u, this._w = c * h * u - d * p * m;
        break;
      case "ZYX":
        this._x = d * h * u - c * p * m, this._y = c * p * u + d * h * m, this._z = c * h * m - d * p * u, this._w = c * h * u + d * p * m;
        break;
      case "YZX":
        this._x = d * h * u + c * p * m, this._y = c * p * u + d * h * m, this._z = c * h * m - d * p * u, this._w = c * h * u - d * p * m;
        break;
      case "XZY":
        this._x = d * h * u - c * p * m, this._y = c * p * u - d * h * m, this._z = c * h * m + d * p * u, this._w = c * h * u + d * p * m;
        break;
      default:
        console.warn("THREE.Quaternion: .setFromEuler() encountered an unknown order: " + r);
    }
    return e === !0 && this._onChangeCallback(), this;
  }
  setFromAxisAngle(t, e) {
    const s = e / 2, i = Math.sin(s);
    return this._x = t.x * i, this._y = t.y * i, this._z = t.z * i, this._w = Math.cos(s), this._onChangeCallback(), this;
  }
  setFromRotationMatrix(t) {
    const e = t.elements, s = e[0], i = e[4], n = e[8], r = e[1], o = e[5], l = e[9], c = e[2], h = e[6], u = e[10], d = s + o + u;
    if (d > 0) {
      const p = 0.5 / Math.sqrt(d + 1);
      this._w = 0.25 / p, this._x = (h - l) * p, this._y = (n - c) * p, this._z = (r - i) * p;
    } else if (s > o && s > u) {
      const p = 2 * Math.sqrt(1 + s - o - u);
      this._w = (h - l) / p, this._x = 0.25 * p, this._y = (i + r) / p, this._z = (n + c) / p;
    } else if (o > u) {
      const p = 2 * Math.sqrt(1 + o - s - u);
      this._w = (n - c) / p, this._x = (i + r) / p, this._y = 0.25 * p, this._z = (l + h) / p;
    } else {
      const p = 2 * Math.sqrt(1 + u - s - o);
      this._w = (r - i) / p, this._x = (n + c) / p, this._y = (l + h) / p, this._z = 0.25 * p;
    }
    return this._onChangeCallback(), this;
  }
  setFromUnitVectors(t, e) {
    let s = t.dot(e) + 1;
    return s < Number.EPSILON ? (s = 0, Math.abs(t.x) > Math.abs(t.z) ? (this._x = -t.y, this._y = t.x, this._z = 0, this._w = s) : (this._x = 0, this._y = -t.z, this._z = t.y, this._w = s)) : (this._x = t.y * e.z - t.z * e.y, this._y = t.z * e.x - t.x * e.z, this._z = t.x * e.y - t.y * e.x, this._w = s), this.normalize();
  }
  angleTo(t) {
    return 2 * Math.acos(Math.abs(T(this.dot(t), -1, 1)));
  }
  rotateTowards(t, e) {
    const s = this.angleTo(t);
    if (s === 0) return this;
    const i = Math.min(1, e / s);
    return this.slerp(t, i), this;
  }
  identity() {
    return this.set(0, 0, 0, 1);
  }
  invert() {
    return this.conjugate();
  }
  conjugate() {
    return this._x *= -1, this._y *= -1, this._z *= -1, this._onChangeCallback(), this;
  }
  dot(t) {
    return this._x * t._x + this._y * t._y + this._z * t._z + this._w * t._w;
  }
  lengthSq() {
    return this._x * this._x + this._y * this._y + this._z * this._z + this._w * this._w;
  }
  length() {
    return Math.sqrt(this._x * this._x + this._y * this._y + this._z * this._z + this._w * this._w);
  }
  normalize() {
    let t = this.length();
    return t === 0 ? (this._x = 0, this._y = 0, this._z = 0, this._w = 1) : (t = 1 / t, this._x = this._x * t, this._y = this._y * t, this._z = this._z * t, this._w = this._w * t), this._onChangeCallback(), this;
  }
  multiply(t) {
    return this.multiplyQuaternions(this, t);
  }
  premultiply(t) {
    return this.multiplyQuaternions(t, this);
  }
  multiplyQuaternions(t, e) {
    const s = t._x, i = t._y, n = t._z, r = t._w, o = e._x, l = e._y, c = e._z, h = e._w;
    return this._x = s * h + r * o + i * c - n * l, this._y = i * h + r * l + n * o - s * c, this._z = n * h + r * c + s * l - i * o, this._w = r * h - s * o - i * l - n * c, this._onChangeCallback(), this;
  }
  slerp(t, e) {
    if (e === 0) return this;
    if (e === 1) return this.copy(t);
    const s = this._x, i = this._y, n = this._z, r = this._w;
    let o = r * t._w + s * t._x + i * t._y + n * t._z;
    if (o < 0 ? (this._w = -t._w, this._x = -t._x, this._y = -t._y, this._z = -t._z, o = -o) : this.copy(t), o >= 1)
      return this._w = r, this._x = s, this._y = i, this._z = n, this;
    const l = 1 - o * o;
    if (l <= Number.EPSILON) {
      const p = 1 - e;
      return this._w = p * r + e * this._w, this._x = p * s + e * this._x, this._y = p * i + e * this._y, this._z = p * n + e * this._z, this.normalize(), this;
    }
    const c = Math.sqrt(l), h = Math.atan2(c, o), u = Math.sin((1 - e) * h) / c, d = Math.sin(e * h) / c;
    return this._w = r * u + this._w * d, this._x = s * u + this._x * d, this._y = i * u + this._y * d, this._z = n * u + this._z * d, this._onChangeCallback(), this;
  }
  slerpQuaternions(t, e, s) {
    return this.copy(t).slerp(e, s);
  }
  random() {
    const t = 2 * Math.PI * Math.random(), e = 2 * Math.PI * Math.random(), s = Math.random(), i = Math.sqrt(1 - s), n = Math.sqrt(s);
    return this.set(
      i * Math.sin(t),
      i * Math.cos(t),
      n * Math.sin(e),
      n * Math.cos(e)
    );
  }
  equals(t) {
    return t._x === this._x && t._y === this._y && t._z === this._z && t._w === this._w;
  }
  fromArray(t, e = 0) {
    return this._x = t[e], this._y = t[e + 1], this._z = t[e + 2], this._w = t[e + 3], this._onChangeCallback(), this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this._x, t[e + 1] = this._y, t[e + 2] = this._z, t[e + 3] = this._w, t;
  }
  fromBufferAttribute(t, e) {
    return this._x = t.getX(e), this._y = t.getY(e), this._z = t.getZ(e), this._w = t.getW(e), this._onChangeCallback(), this;
  }
  toJSON() {
    return this.toArray();
  }
  _onChange(t) {
    return this._onChangeCallback = t, this;
  }
  _onChangeCallback() {
  }
  *[Symbol.iterator]() {
    yield this._x, yield this._y, yield this._z, yield this._w;
  }
};
class R {
  constructor(t = 0, e = 0, s = 0) {
    R.prototype.isVector3 = !0, this.x = t, this.y = e, this.z = s;
  }
  set(t, e, s) {
    return s === void 0 && (s = this.z), this.x = t, this.y = e, this.z = s, this;
  }
  setScalar(t) {
    return this.x = t, this.y = t, this.z = t, this;
  }
  setX(t) {
    return this.x = t, this;
  }
  setY(t) {
    return this.y = t, this;
  }
  setZ(t) {
    return this.z = t, this;
  }
  setComponent(t, e) {
    switch (t) {
      case 0:
        this.x = e;
        break;
      case 1:
        this.y = e;
        break;
      case 2:
        this.z = e;
        break;
      default:
        throw new Error("index is out of range: " + t);
    }
    return this;
  }
  getComponent(t) {
    switch (t) {
      case 0:
        return this.x;
      case 1:
        return this.y;
      case 2:
        return this.z;
      default:
        throw new Error("index is out of range: " + t);
    }
  }
  clone() {
    return new this.constructor(this.x, this.y, this.z);
  }
  copy(t) {
    return this.x = t.x, this.y = t.y, this.z = t.z, this;
  }
  add(t) {
    return this.x += t.x, this.y += t.y, this.z += t.z, this;
  }
  addScalar(t) {
    return this.x += t, this.y += t, this.z += t, this;
  }
  addVectors(t, e) {
    return this.x = t.x + e.x, this.y = t.y + e.y, this.z = t.z + e.z, this;
  }
  addScaledVector(t, e) {
    return this.x += t.x * e, this.y += t.y * e, this.z += t.z * e, this;
  }
  sub(t) {
    return this.x -= t.x, this.y -= t.y, this.z -= t.z, this;
  }
  subScalar(t) {
    return this.x -= t, this.y -= t, this.z -= t, this;
  }
  subVectors(t, e) {
    return this.x = t.x - e.x, this.y = t.y - e.y, this.z = t.z - e.z, this;
  }
  multiply(t) {
    return this.x *= t.x, this.y *= t.y, this.z *= t.z, this;
  }
  multiplyScalar(t) {
    return this.x *= t, this.y *= t, this.z *= t, this;
  }
  multiplyVectors(t, e) {
    return this.x = t.x * e.x, this.y = t.y * e.y, this.z = t.z * e.z, this;
  }
  applyEuler(t) {
    return this.applyQuaternion(Ga.setFromEuler(t));
  }
  applyAxisAngle(t, e) {
    return this.applyQuaternion(Ga.setFromAxisAngle(t, e));
  }
  applyMatrix3(t) {
    const e = this.x, s = this.y, i = this.z, n = t.elements;
    return this.x = n[0] * e + n[3] * s + n[6] * i, this.y = n[1] * e + n[4] * s + n[7] * i, this.z = n[2] * e + n[5] * s + n[8] * i, this;
  }
  applyNormalMatrix(t) {
    return this.applyMatrix3(t).normalize();
  }
  applyMatrix4(t) {
    const e = this.x, s = this.y, i = this.z, n = t.elements, r = 1 / (n[3] * e + n[7] * s + n[11] * i + n[15]);
    return this.x = (n[0] * e + n[4] * s + n[8] * i + n[12]) * r, this.y = (n[1] * e + n[5] * s + n[9] * i + n[13]) * r, this.z = (n[2] * e + n[6] * s + n[10] * i + n[14]) * r, this;
  }
  applyQuaternion(t) {
    const e = this.x, s = this.y, i = this.z, n = t.x, r = t.y, o = t.z, l = t.w, c = 2 * (r * i - o * s), h = 2 * (o * e - n * i), u = 2 * (n * s - r * e);
    return this.x = e + l * c + r * u - o * h, this.y = s + l * h + o * c - n * u, this.z = i + l * u + n * h - r * c, this;
  }
  project(t) {
    return this.applyMatrix4(t.matrixWorldInverse).applyMatrix4(t.projectionMatrix);
  }
  unproject(t) {
    return this.applyMatrix4(t.projectionMatrixInverse).applyMatrix4(t.matrixWorld);
  }
  transformDirection(t) {
    const e = this.x, s = this.y, i = this.z, n = t.elements;
    return this.x = n[0] * e + n[4] * s + n[8] * i, this.y = n[1] * e + n[5] * s + n[9] * i, this.z = n[2] * e + n[6] * s + n[10] * i, this.normalize();
  }
  divide(t) {
    return this.x /= t.x, this.y /= t.y, this.z /= t.z, this;
  }
  divideScalar(t) {
    return this.multiplyScalar(1 / t);
  }
  min(t) {
    return this.x = Math.min(this.x, t.x), this.y = Math.min(this.y, t.y), this.z = Math.min(this.z, t.z), this;
  }
  max(t) {
    return this.x = Math.max(this.x, t.x), this.y = Math.max(this.y, t.y), this.z = Math.max(this.z, t.z), this;
  }
  clamp(t, e) {
    return this.x = T(this.x, t.x, e.x), this.y = T(this.y, t.y, e.y), this.z = T(this.z, t.z, e.z), this;
  }
  clampScalar(t, e) {
    return this.x = T(this.x, t, e), this.y = T(this.y, t, e), this.z = T(this.z, t, e), this;
  }
  clampLength(t, e) {
    const s = this.length();
    return this.divideScalar(s || 1).multiplyScalar(T(s, t, e));
  }
  floor() {
    return this.x = Math.floor(this.x), this.y = Math.floor(this.y), this.z = Math.floor(this.z), this;
  }
  ceil() {
    return this.x = Math.ceil(this.x), this.y = Math.ceil(this.y), this.z = Math.ceil(this.z), this;
  }
  round() {
    return this.x = Math.round(this.x), this.y = Math.round(this.y), this.z = Math.round(this.z), this;
  }
  roundToZero() {
    return this.x = Math.trunc(this.x), this.y = Math.trunc(this.y), this.z = Math.trunc(this.z), this;
  }
  negate() {
    return this.x = -this.x, this.y = -this.y, this.z = -this.z, this;
  }
  dot(t) {
    return this.x * t.x + this.y * t.y + this.z * t.z;
  }
  // TODO lengthSquared?
  lengthSq() {
    return this.x * this.x + this.y * this.y + this.z * this.z;
  }
  length() {
    return Math.sqrt(this.x * this.x + this.y * this.y + this.z * this.z);
  }
  manhattanLength() {
    return Math.abs(this.x) + Math.abs(this.y) + Math.abs(this.z);
  }
  normalize() {
    return this.divideScalar(this.length() || 1);
  }
  setLength(t) {
    return this.normalize().multiplyScalar(t);
  }
  lerp(t, e) {
    return this.x += (t.x - this.x) * e, this.y += (t.y - this.y) * e, this.z += (t.z - this.z) * e, this;
  }
  lerpVectors(t, e, s) {
    return this.x = t.x + (e.x - t.x) * s, this.y = t.y + (e.y - t.y) * s, this.z = t.z + (e.z - t.z) * s, this;
  }
  cross(t) {
    return this.crossVectors(this, t);
  }
  crossVectors(t, e) {
    const s = t.x, i = t.y, n = t.z, r = e.x, o = e.y, l = e.z;
    return this.x = i * l - n * o, this.y = n * r - s * l, this.z = s * o - i * r, this;
  }
  projectOnVector(t) {
    const e = t.lengthSq();
    if (e === 0) return this.set(0, 0, 0);
    const s = t.dot(this) / e;
    return this.copy(t).multiplyScalar(s);
  }
  projectOnPlane(t) {
    return js.copy(this).projectOnVector(t), this.sub(js);
  }
  reflect(t) {
    return this.sub(js.copy(t).multiplyScalar(2 * this.dot(t)));
  }
  angleTo(t) {
    const e = Math.sqrt(this.lengthSq() * t.lengthSq());
    if (e === 0) return Math.PI / 2;
    const s = this.dot(t) / e;
    return Math.acos(T(s, -1, 1));
  }
  distanceTo(t) {
    return Math.sqrt(this.distanceToSquared(t));
  }
  distanceToSquared(t) {
    const e = this.x - t.x, s = this.y - t.y, i = this.z - t.z;
    return e * e + s * s + i * i;
  }
  manhattanDistanceTo(t) {
    return Math.abs(this.x - t.x) + Math.abs(this.y - t.y) + Math.abs(this.z - t.z);
  }
  setFromSpherical(t) {
    return this.setFromSphericalCoords(t.radius, t.phi, t.theta);
  }
  setFromSphericalCoords(t, e, s) {
    const i = Math.sin(e) * t;
    return this.x = i * Math.sin(s), this.y = Math.cos(e) * t, this.z = i * Math.cos(s), this;
  }
  setFromCylindrical(t) {
    return this.setFromCylindricalCoords(t.radius, t.theta, t.y);
  }
  setFromCylindricalCoords(t, e, s) {
    return this.x = t * Math.sin(e), this.y = s, this.z = t * Math.cos(e), this;
  }
  setFromMatrixPosition(t) {
    const e = t.elements;
    return this.x = e[12], this.y = e[13], this.z = e[14], this;
  }
  setFromMatrixScale(t) {
    const e = this.setFromMatrixColumn(t, 0).length(), s = this.setFromMatrixColumn(t, 1).length(), i = this.setFromMatrixColumn(t, 2).length();
    return this.x = e, this.y = s, this.z = i, this;
  }
  setFromMatrixColumn(t, e) {
    return this.fromArray(t.elements, e * 4);
  }
  setFromMatrix3Column(t, e) {
    return this.fromArray(t.elements, e * 3);
  }
  setFromEuler(t) {
    return this.x = t._x, this.y = t._y, this.z = t._z, this;
  }
  setFromColor(t) {
    return this.x = t.r, this.y = t.g, this.z = t.b, this;
  }
  equals(t) {
    return t.x === this.x && t.y === this.y && t.z === this.z;
  }
  fromArray(t, e = 0) {
    return this.x = t[e], this.y = t[e + 1], this.z = t[e + 2], this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this.x, t[e + 1] = this.y, t[e + 2] = this.z, t;
  }
  fromBufferAttribute(t, e) {
    return this.x = t.getX(e), this.y = t.getY(e), this.z = t.getZ(e), this;
  }
  random() {
    return this.x = Math.random(), this.y = Math.random(), this.z = Math.random(), this;
  }
  randomDirection() {
    const t = Math.random() * Math.PI * 2, e = Math.random() * 2 - 1, s = Math.sqrt(1 - e * e);
    return this.x = s * Math.cos(t), this.y = e, this.z = s * Math.sin(t), this;
  }
  *[Symbol.iterator]() {
    yield this.x, yield this.y, yield this.z;
  }
}
const js = /* @__PURE__ */ new R(), Ga = /* @__PURE__ */ new ee();
let St = class {
  constructor(t = new R(1 / 0, 1 / 0, 1 / 0), e = new R(-1 / 0, -1 / 0, -1 / 0)) {
    this.isBox3 = !0, this.min = t, this.max = e;
  }
  set(t, e) {
    return this.min.copy(t), this.max.copy(e), this;
  }
  setFromArray(t) {
    this.makeEmpty();
    for (let e = 0, s = t.length; e < s; e += 3)
      this.expandByPoint(mt.fromArray(t, e));
    return this;
  }
  setFromBufferAttribute(t) {
    this.makeEmpty();
    for (let e = 0, s = t.count; e < s; e++)
      this.expandByPoint(mt.fromBufferAttribute(t, e));
    return this;
  }
  setFromPoints(t) {
    this.makeEmpty();
    for (let e = 0, s = t.length; e < s; e++)
      this.expandByPoint(t[e]);
    return this;
  }
  setFromCenterAndSize(t, e) {
    const s = mt.copy(e).multiplyScalar(0.5);
    return this.min.copy(t).sub(s), this.max.copy(t).add(s), this;
  }
  setFromObject(t, e = !1) {
    return this.makeEmpty(), this.expandByObject(t, e);
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    return this.min.copy(t.min), this.max.copy(t.max), this;
  }
  makeEmpty() {
    return this.min.x = this.min.y = this.min.z = 1 / 0, this.max.x = this.max.y = this.max.z = -1 / 0, this;
  }
  isEmpty() {
    return this.max.x < this.min.x || this.max.y < this.min.y || this.max.z < this.min.z;
  }
  getCenter(t) {
    return this.isEmpty() ? t.set(0, 0, 0) : t.addVectors(this.min, this.max).multiplyScalar(0.5);
  }
  getSize(t) {
    return this.isEmpty() ? t.set(0, 0, 0) : t.subVectors(this.max, this.min);
  }
  expandByPoint(t) {
    return this.min.min(t), this.max.max(t), this;
  }
  expandByVector(t) {
    return this.min.sub(t), this.max.add(t), this;
  }
  expandByScalar(t) {
    return this.min.addScalar(-t), this.max.addScalar(t), this;
  }
  expandByObject(t, e = !1) {
    t.updateWorldMatrix(!1, !1);
    const s = t.geometry;
    if (s !== void 0) {
      const n = s.getAttribute("position");
      if (e === !0 && n !== void 0 && t.isInstancedMesh !== !0)
        for (let r = 0, o = n.count; r < o; r++)
          t.isMesh === !0 ? t.getVertexPosition(r, mt) : mt.fromBufferAttribute(n, r), mt.applyMatrix4(t.matrixWorld), this.expandByPoint(mt);
      else
        t.boundingBox !== void 0 ? (t.boundingBox === null && t.computeBoundingBox(), es.copy(t.boundingBox)) : (s.boundingBox === null && s.computeBoundingBox(), es.copy(s.boundingBox)), es.applyMatrix4(t.matrixWorld), this.union(es);
    }
    const i = t.children;
    for (let n = 0, r = i.length; n < r; n++)
      this.expandByObject(i[n], e);
    return this;
  }
  containsPoint(t) {
    return t.x >= this.min.x && t.x <= this.max.x && t.y >= this.min.y && t.y <= this.max.y && t.z >= this.min.z && t.z <= this.max.z;
  }
  containsBox(t) {
    return this.min.x <= t.min.x && t.max.x <= this.max.x && this.min.y <= t.min.y && t.max.y <= this.max.y && this.min.z <= t.min.z && t.max.z <= this.max.z;
  }
  getParameter(t, e) {
    return e.set(
      (t.x - this.min.x) / (this.max.x - this.min.x),
      (t.y - this.min.y) / (this.max.y - this.min.y),
      (t.z - this.min.z) / (this.max.z - this.min.z)
    );
  }
  intersectsBox(t) {
    return t.max.x >= this.min.x && t.min.x <= this.max.x && t.max.y >= this.min.y && t.min.y <= this.max.y && t.max.z >= this.min.z && t.min.z <= this.max.z;
  }
  intersectsSphere(t) {
    return this.clampPoint(t.center, mt), mt.distanceToSquared(t.center) <= t.radius * t.radius;
  }
  intersectsPlane(t) {
    let e, s;
    return t.normal.x > 0 ? (e = t.normal.x * this.min.x, s = t.normal.x * this.max.x) : (e = t.normal.x * this.max.x, s = t.normal.x * this.min.x), t.normal.y > 0 ? (e += t.normal.y * this.min.y, s += t.normal.y * this.max.y) : (e += t.normal.y * this.max.y, s += t.normal.y * this.min.y), t.normal.z > 0 ? (e += t.normal.z * this.min.z, s += t.normal.z * this.max.z) : (e += t.normal.z * this.max.z, s += t.normal.z * this.min.z), e <= -t.constant && s >= -t.constant;
  }
  intersectsTriangle(t) {
    if (this.isEmpty())
      return !1;
    this.getCenter(we), ss.subVectors(this.max, we), he.subVectors(t.a, we), ue.subVectors(t.b, we), de.subVectors(t.c, we), Nt.subVectors(ue, he), _t.subVectors(de, ue), jt.subVectors(he, de);
    let e = [
      0,
      -Nt.z,
      Nt.y,
      0,
      -_t.z,
      _t.y,
      0,
      -jt.z,
      jt.y,
      Nt.z,
      0,
      -Nt.x,
      _t.z,
      0,
      -_t.x,
      jt.z,
      0,
      -jt.x,
      -Nt.y,
      Nt.x,
      0,
      -_t.y,
      _t.x,
      0,
      -jt.y,
      jt.x,
      0
    ];
    return !Os(e, he, ue, de, ss) || (e = [1, 0, 0, 0, 1, 0, 0, 0, 1], !Os(e, he, ue, de, ss)) ? !1 : (is.crossVectors(Nt, _t), e = [is.x, is.y, is.z], Os(e, he, ue, de, ss));
  }
  clampPoint(t, e) {
    return e.copy(t).clamp(this.min, this.max);
  }
  distanceToPoint(t) {
    return this.clampPoint(t, mt).distanceTo(t);
  }
  getBoundingSphere(t) {
    return this.isEmpty() ? t.makeEmpty() : (this.getCenter(t.center), t.radius = this.getSize(mt).length() * 0.5), t;
  }
  intersect(t) {
    return this.min.max(t.min), this.max.min(t.max), this.isEmpty() && this.makeEmpty(), this;
  }
  union(t) {
    return this.min.min(t.min), this.max.max(t.max), this;
  }
  applyMatrix4(t) {
    return this.isEmpty() ? this : (Mt[0].set(this.min.x, this.min.y, this.min.z).applyMatrix4(t), Mt[1].set(this.min.x, this.min.y, this.max.z).applyMatrix4(t), Mt[2].set(this.min.x, this.max.y, this.min.z).applyMatrix4(t), Mt[3].set(this.min.x, this.max.y, this.max.z).applyMatrix4(t), Mt[4].set(this.max.x, this.min.y, this.min.z).applyMatrix4(t), Mt[5].set(this.max.x, this.min.y, this.max.z).applyMatrix4(t), Mt[6].set(this.max.x, this.max.y, this.min.z).applyMatrix4(t), Mt[7].set(this.max.x, this.max.y, this.max.z).applyMatrix4(t), this.setFromPoints(Mt), this);
  }
  translate(t) {
    return this.min.add(t), this.max.add(t), this;
  }
  equals(t) {
    return t.min.equals(this.min) && t.max.equals(this.max);
  }
};
const Mt = [
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R(),
  /* @__PURE__ */ new R()
], mt = /* @__PURE__ */ new R(), es = /* @__PURE__ */ new St(), he = /* @__PURE__ */ new R(), ue = /* @__PURE__ */ new R(), de = /* @__PURE__ */ new R(), Nt = /* @__PURE__ */ new R(), _t = /* @__PURE__ */ new R(), jt = /* @__PURE__ */ new R(), we = /* @__PURE__ */ new R(), ss = /* @__PURE__ */ new R(), is = /* @__PURE__ */ new R(), Ot = /* @__PURE__ */ new R();
function Os(a, t, e, s, i) {
  for (let n = 0, r = a.length - 3; n <= r; n += 3) {
    Ot.fromArray(a, n);
    const o = i.x * Math.abs(Ot.x) + i.y * Math.abs(Ot.y) + i.z * Math.abs(Ot.z), l = t.dot(Ot), c = e.dot(Ot), h = s.dot(Ot);
    if (Math.max(-Math.max(l, c, h), Math.min(l, c, h)) > o)
      return !1;
  }
  return !0;
}
const Yh = /* @__PURE__ */ new St(), ve = /* @__PURE__ */ new R(), qs = /* @__PURE__ */ new R();
class Er {
  constructor(t = new R(), e = -1) {
    this.isSphere = !0, this.center = t, this.radius = e;
  }
  set(t, e) {
    return this.center.copy(t), this.radius = e, this;
  }
  setFromPoints(t, e) {
    const s = this.center;
    e !== void 0 ? s.copy(e) : Yh.setFromPoints(t).getCenter(s);
    let i = 0;
    for (let n = 0, r = t.length; n < r; n++)
      i = Math.max(i, s.distanceToSquared(t[n]));
    return this.radius = Math.sqrt(i), this;
  }
  copy(t) {
    return this.center.copy(t.center), this.radius = t.radius, this;
  }
  isEmpty() {
    return this.radius < 0;
  }
  makeEmpty() {
    return this.center.set(0, 0, 0), this.radius = -1, this;
  }
  containsPoint(t) {
    return t.distanceToSquared(this.center) <= this.radius * this.radius;
  }
  distanceToPoint(t) {
    return t.distanceTo(this.center) - this.radius;
  }
  intersectsSphere(t) {
    const e = this.radius + t.radius;
    return t.center.distanceToSquared(this.center) <= e * e;
  }
  intersectsBox(t) {
    return t.intersectsSphere(this);
  }
  intersectsPlane(t) {
    return Math.abs(t.distanceToPoint(this.center)) <= this.radius;
  }
  clampPoint(t, e) {
    const s = this.center.distanceToSquared(t);
    return e.copy(t), s > this.radius * this.radius && (e.sub(this.center).normalize(), e.multiplyScalar(this.radius).add(this.center)), e;
  }
  getBoundingBox(t) {
    return this.isEmpty() ? (t.makeEmpty(), t) : (t.set(this.center, this.center), t.expandByScalar(this.radius), t);
  }
  applyMatrix4(t) {
    return this.center.applyMatrix4(t), this.radius = this.radius * t.getMaxScaleOnAxis(), this;
  }
  translate(t) {
    return this.center.add(t), this;
  }
  expandByPoint(t) {
    if (this.isEmpty())
      return this.center.copy(t), this.radius = 0, this;
    ve.subVectors(t, this.center);
    const e = ve.lengthSq();
    if (e > this.radius * this.radius) {
      const s = Math.sqrt(e), i = (s - this.radius) * 0.5;
      this.center.addScaledVector(ve, i / s), this.radius += i;
    }
    return this;
  }
  union(t) {
    return t.isEmpty() ? this : this.isEmpty() ? (this.copy(t), this) : (this.center.equals(t.center) === !0 ? this.radius = Math.max(this.radius, t.radius) : (qs.subVectors(t.center, this.center).setLength(t.radius), this.expandByPoint(ve.copy(t.center).add(qs)), this.expandByPoint(ve.copy(t.center).sub(qs))), this);
  }
  equals(t) {
    return t.center.equals(this.center) && t.radius === this.radius;
  }
  clone() {
    return new this.constructor().copy(this);
  }
}
const Lt = /* @__PURE__ */ new R(), $s = /* @__PURE__ */ new R(), ns = /* @__PURE__ */ new R(), Ht = /* @__PURE__ */ new R(), ti = /* @__PURE__ */ new R(), rs = /* @__PURE__ */ new R(), ei = /* @__PURE__ */ new R();
class Br {
  constructor(t = new R(), e = new R(0, 0, -1)) {
    this.origin = t, this.direction = e;
  }
  set(t, e) {
    return this.origin.copy(t), this.direction.copy(e), this;
  }
  copy(t) {
    return this.origin.copy(t.origin), this.direction.copy(t.direction), this;
  }
  at(t, e) {
    return e.copy(this.origin).addScaledVector(this.direction, t);
  }
  lookAt(t) {
    return this.direction.copy(t).sub(this.origin).normalize(), this;
  }
  recast(t) {
    return this.origin.copy(this.at(t, Lt)), this;
  }
  closestPointToPoint(t, e) {
    e.subVectors(t, this.origin);
    const s = e.dot(this.direction);
    return s < 0 ? e.copy(this.origin) : e.copy(this.origin).addScaledVector(this.direction, s);
  }
  distanceToPoint(t) {
    return Math.sqrt(this.distanceSqToPoint(t));
  }
  distanceSqToPoint(t) {
    const e = Lt.subVectors(t, this.origin).dot(this.direction);
    return e < 0 ? this.origin.distanceToSquared(t) : (Lt.copy(this.origin).addScaledVector(this.direction, e), Lt.distanceToSquared(t));
  }
  distanceSqToSegment(t, e, s, i) {
    $s.copy(t).add(e).multiplyScalar(0.5), ns.copy(e).sub(t).normalize(), Ht.copy(this.origin).sub($s);
    const n = t.distanceTo(e) * 0.5, r = -this.direction.dot(ns), o = Ht.dot(this.direction), l = -Ht.dot(ns), c = Ht.lengthSq(), h = Math.abs(1 - r * r);
    let u, d, p, m;
    if (h > 0)
      if (u = r * l - o, d = r * o - l, m = n * h, u >= 0)
        if (d >= -m)
          if (d <= m) {
            const f = 1 / h;
            u *= f, d *= f, p = u * (u + r * d + 2 * o) + d * (r * u + d + 2 * l) + c;
          } else
            d = n, u = Math.max(0, -(r * d + o)), p = -u * u + d * (d + 2 * l) + c;
        else
          d = -n, u = Math.max(0, -(r * d + o)), p = -u * u + d * (d + 2 * l) + c;
      else
        d <= -m ? (u = Math.max(0, -(-r * n + o)), d = u > 0 ? -n : Math.min(Math.max(-n, -l), n), p = -u * u + d * (d + 2 * l) + c) : d <= m ? (u = 0, d = Math.min(Math.max(-n, -l), n), p = d * (d + 2 * l) + c) : (u = Math.max(0, -(r * n + o)), d = u > 0 ? n : Math.min(Math.max(-n, -l), n), p = -u * u + d * (d + 2 * l) + c);
    else
      d = r > 0 ? -n : n, u = Math.max(0, -(r * d + o)), p = -u * u + d * (d + 2 * l) + c;
    return s && s.copy(this.origin).addScaledVector(this.direction, u), i && i.copy($s).addScaledVector(ns, d), p;
  }
  intersectSphere(t, e) {
    Lt.subVectors(t.center, this.origin);
    const s = Lt.dot(this.direction), i = Lt.dot(Lt) - s * s, n = t.radius * t.radius;
    if (i > n) return null;
    const r = Math.sqrt(n - i), o = s - r, l = s + r;
    return l < 0 ? null : o < 0 ? this.at(l, e) : this.at(o, e);
  }
  intersectsSphere(t) {
    return this.distanceSqToPoint(t.center) <= t.radius * t.radius;
  }
  distanceToPlane(t) {
    const e = t.normal.dot(this.direction);
    if (e === 0)
      return t.distanceToPoint(this.origin) === 0 ? 0 : null;
    const s = -(this.origin.dot(t.normal) + t.constant) / e;
    return s >= 0 ? s : null;
  }
  intersectPlane(t, e) {
    const s = this.distanceToPlane(t);
    return s === null ? null : this.at(s, e);
  }
  intersectsPlane(t) {
    const e = t.distanceToPoint(this.origin);
    return e === 0 || t.normal.dot(this.direction) * e < 0;
  }
  intersectBox(t, e) {
    let s, i, n, r, o, l;
    const c = 1 / this.direction.x, h = 1 / this.direction.y, u = 1 / this.direction.z, d = this.origin;
    return c >= 0 ? (s = (t.min.x - d.x) * c, i = (t.max.x - d.x) * c) : (s = (t.max.x - d.x) * c, i = (t.min.x - d.x) * c), h >= 0 ? (n = (t.min.y - d.y) * h, r = (t.max.y - d.y) * h) : (n = (t.max.y - d.y) * h, r = (t.min.y - d.y) * h), s > r || n > i || ((n > s || isNaN(s)) && (s = n), (r < i || isNaN(i)) && (i = r), u >= 0 ? (o = (t.min.z - d.z) * u, l = (t.max.z - d.z) * u) : (o = (t.max.z - d.z) * u, l = (t.min.z - d.z) * u), s > l || o > i) || ((o > s || s !== s) && (s = o), (l < i || i !== i) && (i = l), i < 0) ? null : this.at(s >= 0 ? s : i, e);
  }
  intersectsBox(t) {
    return this.intersectBox(t, Lt) !== null;
  }
  intersectTriangle(t, e, s, i, n) {
    ti.subVectors(e, t), rs.subVectors(s, t), ei.crossVectors(ti, rs);
    let r = this.direction.dot(ei), o;
    if (r > 0) {
      if (i) return null;
      o = 1;
    } else if (r < 0)
      o = -1, r = -r;
    else
      return null;
    Ht.subVectors(this.origin, t);
    const l = o * this.direction.dot(rs.crossVectors(Ht, rs));
    if (l < 0)
      return null;
    const c = o * this.direction.dot(ti.cross(Ht));
    if (c < 0 || l + c > r)
      return null;
    const h = -o * Ht.dot(ei);
    return h < 0 ? null : this.at(h / r, n);
  }
  applyMatrix4(t) {
    return this.origin.applyMatrix4(t), this.direction.transformDirection(t), this;
  }
  equals(t) {
    return t.origin.equals(this.origin) && t.direction.equals(this.direction);
  }
  clone() {
    return new this.constructor().copy(this);
  }
}
class nt {
  constructor(t, e, s, i, n, r, o, l, c, h, u, d, p, m, f, b) {
    nt.prototype.isMatrix4 = !0, this.elements = [
      1,
      0,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      0,
      1
    ], t !== void 0 && this.set(t, e, s, i, n, r, o, l, c, h, u, d, p, m, f, b);
  }
  set(t, e, s, i, n, r, o, l, c, h, u, d, p, m, f, b) {
    const y = this.elements;
    return y[0] = t, y[4] = e, y[8] = s, y[12] = i, y[1] = n, y[5] = r, y[9] = o, y[13] = l, y[2] = c, y[6] = h, y[10] = u, y[14] = d, y[3] = p, y[7] = m, y[11] = f, y[15] = b, this;
  }
  identity() {
    return this.set(
      1,
      0,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  clone() {
    return new nt().fromArray(this.elements);
  }
  copy(t) {
    const e = this.elements, s = t.elements;
    return e[0] = s[0], e[1] = s[1], e[2] = s[2], e[3] = s[3], e[4] = s[4], e[5] = s[5], e[6] = s[6], e[7] = s[7], e[8] = s[8], e[9] = s[9], e[10] = s[10], e[11] = s[11], e[12] = s[12], e[13] = s[13], e[14] = s[14], e[15] = s[15], this;
  }
  copyPosition(t) {
    const e = this.elements, s = t.elements;
    return e[12] = s[12], e[13] = s[13], e[14] = s[14], this;
  }
  setFromMatrix3(t) {
    const e = t.elements;
    return this.set(
      e[0],
      e[3],
      e[6],
      0,
      e[1],
      e[4],
      e[7],
      0,
      e[2],
      e[5],
      e[8],
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  extractBasis(t, e, s) {
    return t.setFromMatrixColumn(this, 0), e.setFromMatrixColumn(this, 1), s.setFromMatrixColumn(this, 2), this;
  }
  makeBasis(t, e, s) {
    return this.set(
      t.x,
      e.x,
      s.x,
      0,
      t.y,
      e.y,
      s.y,
      0,
      t.z,
      e.z,
      s.z,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  extractRotation(t) {
    const e = this.elements, s = t.elements, i = 1 / pe.setFromMatrixColumn(t, 0).length(), n = 1 / pe.setFromMatrixColumn(t, 1).length(), r = 1 / pe.setFromMatrixColumn(t, 2).length();
    return e[0] = s[0] * i, e[1] = s[1] * i, e[2] = s[2] * i, e[3] = 0, e[4] = s[4] * n, e[5] = s[5] * n, e[6] = s[6] * n, e[7] = 0, e[8] = s[8] * r, e[9] = s[9] * r, e[10] = s[10] * r, e[11] = 0, e[12] = 0, e[13] = 0, e[14] = 0, e[15] = 1, this;
  }
  makeRotationFromEuler(t) {
    const e = this.elements, s = t.x, i = t.y, n = t.z, r = Math.cos(s), o = Math.sin(s), l = Math.cos(i), c = Math.sin(i), h = Math.cos(n), u = Math.sin(n);
    if (t.order === "XYZ") {
      const d = r * h, p = r * u, m = o * h, f = o * u;
      e[0] = l * h, e[4] = -l * u, e[8] = c, e[1] = p + m * c, e[5] = d - f * c, e[9] = -o * l, e[2] = f - d * c, e[6] = m + p * c, e[10] = r * l;
    } else if (t.order === "YXZ") {
      const d = l * h, p = l * u, m = c * h, f = c * u;
      e[0] = d + f * o, e[4] = m * o - p, e[8] = r * c, e[1] = r * u, e[5] = r * h, e[9] = -o, e[2] = p * o - m, e[6] = f + d * o, e[10] = r * l;
    } else if (t.order === "ZXY") {
      const d = l * h, p = l * u, m = c * h, f = c * u;
      e[0] = d - f * o, e[4] = -r * u, e[8] = m + p * o, e[1] = p + m * o, e[5] = r * h, e[9] = f - d * o, e[2] = -r * c, e[6] = o, e[10] = r * l;
    } else if (t.order === "ZYX") {
      const d = r * h, p = r * u, m = o * h, f = o * u;
      e[0] = l * h, e[4] = m * c - p, e[8] = d * c + f, e[1] = l * u, e[5] = f * c + d, e[9] = p * c - m, e[2] = -c, e[6] = o * l, e[10] = r * l;
    } else if (t.order === "YZX") {
      const d = r * l, p = r * c, m = o * l, f = o * c;
      e[0] = l * h, e[4] = f - d * u, e[8] = m * u + p, e[1] = u, e[5] = r * h, e[9] = -o * h, e[2] = -c * h, e[6] = p * u + m, e[10] = d - f * u;
    } else if (t.order === "XZY") {
      const d = r * l, p = r * c, m = o * l, f = o * c;
      e[0] = l * h, e[4] = -u, e[8] = c * h, e[1] = d * u + f, e[5] = r * h, e[9] = p * u - m, e[2] = m * u - p, e[6] = o * h, e[10] = f * u + d;
    }
    return e[3] = 0, e[7] = 0, e[11] = 0, e[12] = 0, e[13] = 0, e[14] = 0, e[15] = 1, this;
  }
  makeRotationFromQuaternion(t) {
    return this.compose(Ph, t, Ah);
  }
  lookAt(t, e, s) {
    const i = this.elements;
    return lt.subVectors(t, e), lt.lengthSq() === 0 && (lt.z = 1), lt.normalize(), Yt.crossVectors(s, lt), Yt.lengthSq() === 0 && (Math.abs(s.z) === 1 ? lt.x += 1e-4 : lt.z += 1e-4, lt.normalize(), Yt.crossVectors(s, lt)), Yt.normalize(), as.crossVectors(lt, Yt), i[0] = Yt.x, i[4] = as.x, i[8] = lt.x, i[1] = Yt.y, i[5] = as.y, i[9] = lt.y, i[2] = Yt.z, i[6] = as.z, i[10] = lt.z, this;
  }
  multiply(t) {
    return this.multiplyMatrices(this, t);
  }
  premultiply(t) {
    return this.multiplyMatrices(t, this);
  }
  multiplyMatrices(t, e) {
    const s = t.elements, i = e.elements, n = this.elements, r = s[0], o = s[4], l = s[8], c = s[12], h = s[1], u = s[5], d = s[9], p = s[13], m = s[2], f = s[6], b = s[10], y = s[14], x = s[3], g = s[7], S = s[11], Z = s[15], V = i[0], G = i[4], M = i[8], L = i[12], k = i[1], w = i[5], E = i[9], F = i[13], X = i[2], I = i[6], H = i[10], ot = i[14], $ = i[3], N = i[7], _ = i[11], K = i[15];
    return n[0] = r * V + o * k + l * X + c * $, n[4] = r * G + o * w + l * I + c * N, n[8] = r * M + o * E + l * H + c * _, n[12] = r * L + o * F + l * ot + c * K, n[1] = h * V + u * k + d * X + p * $, n[5] = h * G + u * w + d * I + p * N, n[9] = h * M + u * E + d * H + p * _, n[13] = h * L + u * F + d * ot + p * K, n[2] = m * V + f * k + b * X + y * $, n[6] = m * G + f * w + b * I + y * N, n[10] = m * M + f * E + b * H + y * _, n[14] = m * L + f * F + b * ot + y * K, n[3] = x * V + g * k + S * X + Z * $, n[7] = x * G + g * w + S * I + Z * N, n[11] = x * M + g * E + S * H + Z * _, n[15] = x * L + g * F + S * ot + Z * K, this;
  }
  multiplyScalar(t) {
    const e = this.elements;
    return e[0] *= t, e[4] *= t, e[8] *= t, e[12] *= t, e[1] *= t, e[5] *= t, e[9] *= t, e[13] *= t, e[2] *= t, e[6] *= t, e[10] *= t, e[14] *= t, e[3] *= t, e[7] *= t, e[11] *= t, e[15] *= t, this;
  }
  determinant() {
    const t = this.elements, e = t[0], s = t[4], i = t[8], n = t[12], r = t[1], o = t[5], l = t[9], c = t[13], h = t[2], u = t[6], d = t[10], p = t[14], m = t[3], f = t[7], b = t[11], y = t[15];
    return m * (+n * l * u - i * c * u - n * o * d + s * c * d + i * o * p - s * l * p) + f * (+e * l * p - e * c * d + n * r * d - i * r * p + i * c * h - n * l * h) + b * (+e * c * u - e * o * p - n * r * u + s * r * p + n * o * h - s * c * h) + y * (-i * o * h - e * l * u + e * o * d + i * r * u - s * r * d + s * l * h);
  }
  transpose() {
    const t = this.elements;
    let e;
    return e = t[1], t[1] = t[4], t[4] = e, e = t[2], t[2] = t[8], t[8] = e, e = t[6], t[6] = t[9], t[9] = e, e = t[3], t[3] = t[12], t[12] = e, e = t[7], t[7] = t[13], t[13] = e, e = t[11], t[11] = t[14], t[14] = e, this;
  }
  setPosition(t, e, s) {
    const i = this.elements;
    return t.isVector3 ? (i[12] = t.x, i[13] = t.y, i[14] = t.z) : (i[12] = t, i[13] = e, i[14] = s), this;
  }
  invert() {
    const t = this.elements, e = t[0], s = t[1], i = t[2], n = t[3], r = t[4], o = t[5], l = t[6], c = t[7], h = t[8], u = t[9], d = t[10], p = t[11], m = t[12], f = t[13], b = t[14], y = t[15], x = u * b * c - f * d * c + f * l * p - o * b * p - u * l * y + o * d * y, g = m * d * c - h * b * c - m * l * p + r * b * p + h * l * y - r * d * y, S = h * f * c - m * u * c + m * o * p - r * f * p - h * o * y + r * u * y, Z = m * u * l - h * f * l - m * o * d + r * f * d + h * o * b - r * u * b, V = e * x + s * g + i * S + n * Z;
    if (V === 0) return this.set(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    const G = 1 / V;
    return t[0] = x * G, t[1] = (f * d * n - u * b * n - f * i * p + s * b * p + u * i * y - s * d * y) * G, t[2] = (o * b * n - f * l * n + f * i * c - s * b * c - o * i * y + s * l * y) * G, t[3] = (u * l * n - o * d * n - u * i * c + s * d * c + o * i * p - s * l * p) * G, t[4] = g * G, t[5] = (h * b * n - m * d * n + m * i * p - e * b * p - h * i * y + e * d * y) * G, t[6] = (m * l * n - r * b * n - m * i * c + e * b * c + r * i * y - e * l * y) * G, t[7] = (r * d * n - h * l * n + h * i * c - e * d * c - r * i * p + e * l * p) * G, t[8] = S * G, t[9] = (m * u * n - h * f * n - m * s * p + e * f * p + h * s * y - e * u * y) * G, t[10] = (r * f * n - m * o * n + m * s * c - e * f * c - r * s * y + e * o * y) * G, t[11] = (h * o * n - r * u * n - h * s * c + e * u * c + r * s * p - e * o * p) * G, t[12] = Z * G, t[13] = (h * f * i - m * u * i + m * s * d - e * f * d - h * s * b + e * u * b) * G, t[14] = (m * o * i - r * f * i - m * s * l + e * f * l + r * s * b - e * o * b) * G, t[15] = (r * u * i - h * o * i + h * s * l - e * u * l - r * s * d + e * o * d) * G, this;
  }
  scale(t) {
    const e = this.elements, s = t.x, i = t.y, n = t.z;
    return e[0] *= s, e[4] *= i, e[8] *= n, e[1] *= s, e[5] *= i, e[9] *= n, e[2] *= s, e[6] *= i, e[10] *= n, e[3] *= s, e[7] *= i, e[11] *= n, this;
  }
  getMaxScaleOnAxis() {
    const t = this.elements, e = t[0] * t[0] + t[1] * t[1] + t[2] * t[2], s = t[4] * t[4] + t[5] * t[5] + t[6] * t[6], i = t[8] * t[8] + t[9] * t[9] + t[10] * t[10];
    return Math.sqrt(Math.max(e, s, i));
  }
  makeTranslation(t, e, s) {
    return t.isVector3 ? this.set(
      1,
      0,
      0,
      t.x,
      0,
      1,
      0,
      t.y,
      0,
      0,
      1,
      t.z,
      0,
      0,
      0,
      1
    ) : this.set(
      1,
      0,
      0,
      t,
      0,
      1,
      0,
      e,
      0,
      0,
      1,
      s,
      0,
      0,
      0,
      1
    ), this;
  }
  makeRotationX(t) {
    const e = Math.cos(t), s = Math.sin(t);
    return this.set(
      1,
      0,
      0,
      0,
      0,
      e,
      -s,
      0,
      0,
      s,
      e,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  makeRotationY(t) {
    const e = Math.cos(t), s = Math.sin(t);
    return this.set(
      e,
      0,
      s,
      0,
      0,
      1,
      0,
      0,
      -s,
      0,
      e,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  makeRotationZ(t) {
    const e = Math.cos(t), s = Math.sin(t);
    return this.set(
      e,
      -s,
      0,
      0,
      s,
      e,
      0,
      0,
      0,
      0,
      1,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  makeRotationAxis(t, e) {
    const s = Math.cos(e), i = Math.sin(e), n = 1 - s, r = t.x, o = t.y, l = t.z, c = n * r, h = n * o;
    return this.set(
      c * r + s,
      c * o - i * l,
      c * l + i * o,
      0,
      c * o + i * l,
      h * o + s,
      h * l - i * r,
      0,
      c * l - i * o,
      h * l + i * r,
      n * l * l + s,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  makeScale(t, e, s) {
    return this.set(
      t,
      0,
      0,
      0,
      0,
      e,
      0,
      0,
      0,
      0,
      s,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  makeShear(t, e, s, i, n, r) {
    return this.set(
      1,
      s,
      n,
      0,
      t,
      1,
      r,
      0,
      e,
      i,
      1,
      0,
      0,
      0,
      0,
      1
    ), this;
  }
  compose(t, e, s) {
    const i = this.elements, n = e._x, r = e._y, o = e._z, l = e._w, c = n + n, h = r + r, u = o + o, d = n * c, p = n * h, m = n * u, f = r * h, b = r * u, y = o * u, x = l * c, g = l * h, S = l * u, Z = s.x, V = s.y, G = s.z;
    return i[0] = (1 - (f + y)) * Z, i[1] = (p + S) * Z, i[2] = (m - g) * Z, i[3] = 0, i[4] = (p - S) * V, i[5] = (1 - (d + y)) * V, i[6] = (b + x) * V, i[7] = 0, i[8] = (m + g) * G, i[9] = (b - x) * G, i[10] = (1 - (d + f)) * G, i[11] = 0, i[12] = t.x, i[13] = t.y, i[14] = t.z, i[15] = 1, this;
  }
  decompose(t, e, s) {
    const i = this.elements;
    let n = pe.set(i[0], i[1], i[2]).length();
    const r = pe.set(i[4], i[5], i[6]).length(), o = pe.set(i[8], i[9], i[10]).length();
    this.determinant() < 0 && (n = -n), t.x = i[12], t.y = i[13], t.z = i[14], ft.copy(this);
    const c = 1 / n, h = 1 / r, u = 1 / o;
    return ft.elements[0] *= c, ft.elements[1] *= c, ft.elements[2] *= c, ft.elements[4] *= h, ft.elements[5] *= h, ft.elements[6] *= h, ft.elements[8] *= u, ft.elements[9] *= u, ft.elements[10] *= u, e.setFromRotationMatrix(ft), s.x = n, s.y = r, s.z = o, this;
  }
  makePerspective(t, e, s, i, n, r, o = 2e3) {
    const l = this.elements, c = 2 * n / (e - t), h = 2 * n / (s - i), u = (e + t) / (e - t), d = (s + i) / (s - i);
    let p, m;
    if (o === 2e3)
      p = -(r + n) / (r - n), m = -2 * r * n / (r - n);
    else if (o === 2001)
      p = -r / (r - n), m = -r * n / (r - n);
    else
      throw new Error("THREE.Matrix4.makePerspective(): Invalid coordinate system: " + o);
    return l[0] = c, l[4] = 0, l[8] = u, l[12] = 0, l[1] = 0, l[5] = h, l[9] = d, l[13] = 0, l[2] = 0, l[6] = 0, l[10] = p, l[14] = m, l[3] = 0, l[7] = 0, l[11] = -1, l[15] = 0, this;
  }
  makeOrthographic(t, e, s, i, n, r, o = 2e3) {
    const l = this.elements, c = 1 / (e - t), h = 1 / (s - i), u = 1 / (r - n), d = (e + t) * c, p = (s + i) * h;
    let m, f;
    if (o === 2e3)
      m = (r + n) * u, f = -2 * u;
    else if (o === 2001)
      m = n * u, f = -1 * u;
    else
      throw new Error("THREE.Matrix4.makeOrthographic(): Invalid coordinate system: " + o);
    return l[0] = 2 * c, l[4] = 0, l[8] = 0, l[12] = -d, l[1] = 0, l[5] = 2 * h, l[9] = 0, l[13] = -p, l[2] = 0, l[6] = 0, l[10] = f, l[14] = -m, l[3] = 0, l[7] = 0, l[11] = 0, l[15] = 1, this;
  }
  equals(t) {
    const e = this.elements, s = t.elements;
    for (let i = 0; i < 16; i++)
      if (e[i] !== s[i]) return !1;
    return !0;
  }
  fromArray(t, e = 0) {
    for (let s = 0; s < 16; s++)
      this.elements[s] = t[s + e];
    return this;
  }
  toArray(t = [], e = 0) {
    const s = this.elements;
    return t[e] = s[0], t[e + 1] = s[1], t[e + 2] = s[2], t[e + 3] = s[3], t[e + 4] = s[4], t[e + 5] = s[5], t[e + 6] = s[6], t[e + 7] = s[7], t[e + 8] = s[8], t[e + 9] = s[9], t[e + 10] = s[10], t[e + 11] = s[11], t[e + 12] = s[12], t[e + 13] = s[13], t[e + 14] = s[14], t[e + 15] = s[15], t;
  }
}
const pe = /* @__PURE__ */ new R(), ft = /* @__PURE__ */ new nt(), Ph = /* @__PURE__ */ new R(0, 0, 0), Ah = /* @__PURE__ */ new R(1, 1, 1), Yt = /* @__PURE__ */ new R(), as = /* @__PURE__ */ new R(), lt = /* @__PURE__ */ new R(), Va = /* @__PURE__ */ new nt(), Ma = /* @__PURE__ */ new ee();
class Me {
  constructor(t = 0, e = 0, s = 0, i = Me.DEFAULT_ORDER) {
    this.isEuler = !0, this._x = t, this._y = e, this._z = s, this._order = i;
  }
  get x() {
    return this._x;
  }
  set x(t) {
    this._x = t, this._onChangeCallback();
  }
  get y() {
    return this._y;
  }
  set y(t) {
    this._y = t, this._onChangeCallback();
  }
  get z() {
    return this._z;
  }
  set z(t) {
    this._z = t, this._onChangeCallback();
  }
  get order() {
    return this._order;
  }
  set order(t) {
    this._order = t, this._onChangeCallback();
  }
  set(t, e, s, i = this._order) {
    return this._x = t, this._y = e, this._z = s, this._order = i, this._onChangeCallback(), this;
  }
  clone() {
    return new this.constructor(this._x, this._y, this._z, this._order);
  }
  copy(t) {
    return this._x = t._x, this._y = t._y, this._z = t._z, this._order = t._order, this._onChangeCallback(), this;
  }
  setFromRotationMatrix(t, e = this._order, s = !0) {
    const i = t.elements, n = i[0], r = i[4], o = i[8], l = i[1], c = i[5], h = i[9], u = i[2], d = i[6], p = i[10];
    switch (e) {
      case "XYZ":
        this._y = Math.asin(T(o, -1, 1)), Math.abs(o) < 0.9999999 ? (this._x = Math.atan2(-h, p), this._z = Math.atan2(-r, n)) : (this._x = Math.atan2(d, c), this._z = 0);
        break;
      case "YXZ":
        this._x = Math.asin(-T(h, -1, 1)), Math.abs(h) < 0.9999999 ? (this._y = Math.atan2(o, p), this._z = Math.atan2(l, c)) : (this._y = Math.atan2(-u, n), this._z = 0);
        break;
      case "ZXY":
        this._x = Math.asin(T(d, -1, 1)), Math.abs(d) < 0.9999999 ? (this._y = Math.atan2(-u, p), this._z = Math.atan2(-r, c)) : (this._y = 0, this._z = Math.atan2(l, n));
        break;
      case "ZYX":
        this._y = Math.asin(-T(u, -1, 1)), Math.abs(u) < 0.9999999 ? (this._x = Math.atan2(d, p), this._z = Math.atan2(l, n)) : (this._x = 0, this._z = Math.atan2(-r, c));
        break;
      case "YZX":
        this._z = Math.asin(T(l, -1, 1)), Math.abs(l) < 0.9999999 ? (this._x = Math.atan2(-h, c), this._y = Math.atan2(-u, n)) : (this._x = 0, this._y = Math.atan2(o, p));
        break;
      case "XZY":
        this._z = Math.asin(-T(r, -1, 1)), Math.abs(r) < 0.9999999 ? (this._x = Math.atan2(d, c), this._y = Math.atan2(o, n)) : (this._x = Math.atan2(-h, p), this._y = 0);
        break;
      default:
        console.warn("THREE.Euler: .setFromRotationMatrix() encountered an unknown order: " + e);
    }
    return this._order = e, s === !0 && this._onChangeCallback(), this;
  }
  setFromQuaternion(t, e, s) {
    return Va.makeRotationFromQuaternion(t), this.setFromRotationMatrix(Va, e, s);
  }
  setFromVector3(t, e = this._order) {
    return this.set(t.x, t.y, t.z, e);
  }
  reorder(t) {
    return Ma.setFromEuler(this), this.setFromQuaternion(Ma, t);
  }
  equals(t) {
    return t._x === this._x && t._y === this._y && t._z === this._z && t._order === this._order;
  }
  fromArray(t) {
    return this._x = t[0], this._y = t[1], this._z = t[2], t[3] !== void 0 && (this._order = t[3]), this._onChangeCallback(), this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this._x, t[e + 1] = this._y, t[e + 2] = this._z, t[e + 3] = this._order, t;
  }
  _onChange(t) {
    return this._onChangeCallback = t, this;
  }
  _onChangeCallback() {
  }
  *[Symbol.iterator]() {
    yield this._x, yield this._y, yield this._z, yield this._order;
  }
}
Me.DEFAULT_ORDER = "XYZ";
class Rc {
  constructor() {
    this.mask = 1;
  }
  set(t) {
    this.mask = (1 << t | 0) >>> 0;
  }
  enable(t) {
    this.mask |= 1 << t | 0;
  }
  enableAll() {
    this.mask = -1;
  }
  toggle(t) {
    this.mask ^= 1 << t | 0;
  }
  disable(t) {
    this.mask &= ~(1 << t | 0);
  }
  disableAll() {
    this.mask = 0;
  }
  test(t) {
    return (this.mask & t.mask) !== 0;
  }
  isEnabled(t) {
    return (this.mask & (1 << t | 0)) !== 0;
  }
}
let Kh = 0;
const La = /* @__PURE__ */ new R(), me = /* @__PURE__ */ new ee(), wt = /* @__PURE__ */ new nt(), os = /* @__PURE__ */ new R(), Fe = /* @__PURE__ */ new R(), Jh = /* @__PURE__ */ new R(), Dh = /* @__PURE__ */ new ee(), wa = /* @__PURE__ */ new R(1, 0, 0), va = /* @__PURE__ */ new R(0, 1, 0), Fa = /* @__PURE__ */ new R(0, 0, 1), ka = { type: "added" }, Qh = { type: "removed" }, fe = { type: "childadded", child: null }, si = { type: "childremoved", child: null };
class Rt extends Fs {
  constructor() {
    super(), this.isObject3D = !0, Object.defineProperty(this, "id", { value: Kh++ }), this.uuid = Ve(), this.name = "", this.type = "Object3D", this.parent = null, this.children = [], this.up = Rt.DEFAULT_UP.clone();
    const t = new R(), e = new Me(), s = new ee(), i = new R(1, 1, 1);
    function n() {
      s.setFromEuler(e, !1);
    }
    function r() {
      e.setFromQuaternion(s, void 0, !1);
    }
    e._onChange(n), s._onChange(r), Object.defineProperties(this, {
      position: {
        configurable: !0,
        enumerable: !0,
        value: t
      },
      rotation: {
        configurable: !0,
        enumerable: !0,
        value: e
      },
      quaternion: {
        configurable: !0,
        enumerable: !0,
        value: s
      },
      scale: {
        configurable: !0,
        enumerable: !0,
        value: i
      },
      modelViewMatrix: {
        value: new nt()
      },
      normalMatrix: {
        value: new Ct()
      }
    }), this.matrix = new nt(), this.matrixWorld = new nt(), this.matrixAutoUpdate = Rt.DEFAULT_MATRIX_AUTO_UPDATE, this.matrixWorldAutoUpdate = Rt.DEFAULT_MATRIX_WORLD_AUTO_UPDATE, this.matrixWorldNeedsUpdate = !1, this.layers = new Rc(), this.visible = !0, this.castShadow = !1, this.receiveShadow = !1, this.frustumCulled = !0, this.renderOrder = 0, this.animations = [], this.userData = {};
  }
  onBeforeShadow() {
  }
  onAfterShadow() {
  }
  onBeforeRender() {
  }
  onAfterRender() {
  }
  applyMatrix4(t) {
    this.matrixAutoUpdate && this.updateMatrix(), this.matrix.premultiply(t), this.matrix.decompose(this.position, this.quaternion, this.scale);
  }
  applyQuaternion(t) {
    return this.quaternion.premultiply(t), this;
  }
  setRotationFromAxisAngle(t, e) {
    this.quaternion.setFromAxisAngle(t, e);
  }
  setRotationFromEuler(t) {
    this.quaternion.setFromEuler(t, !0);
  }
  setRotationFromMatrix(t) {
    this.quaternion.setFromRotationMatrix(t);
  }
  setRotationFromQuaternion(t) {
    this.quaternion.copy(t);
  }
  rotateOnAxis(t, e) {
    return me.setFromAxisAngle(t, e), this.quaternion.multiply(me), this;
  }
  rotateOnWorldAxis(t, e) {
    return me.setFromAxisAngle(t, e), this.quaternion.premultiply(me), this;
  }
  rotateX(t) {
    return this.rotateOnAxis(wa, t);
  }
  rotateY(t) {
    return this.rotateOnAxis(va, t);
  }
  rotateZ(t) {
    return this.rotateOnAxis(Fa, t);
  }
  translateOnAxis(t, e) {
    return La.copy(t).applyQuaternion(this.quaternion), this.position.add(La.multiplyScalar(e)), this;
  }
  translateX(t) {
    return this.translateOnAxis(wa, t);
  }
  translateY(t) {
    return this.translateOnAxis(va, t);
  }
  translateZ(t) {
    return this.translateOnAxis(Fa, t);
  }
  localToWorld(t) {
    return this.updateWorldMatrix(!0, !1), t.applyMatrix4(this.matrixWorld);
  }
  worldToLocal(t) {
    return this.updateWorldMatrix(!0, !1), t.applyMatrix4(wt.copy(this.matrixWorld).invert());
  }
  lookAt(t, e, s) {
    t.isVector3 ? os.copy(t) : os.set(t, e, s);
    const i = this.parent;
    this.updateWorldMatrix(!0, !1), Fe.setFromMatrixPosition(this.matrixWorld), this.isCamera || this.isLight ? wt.lookAt(Fe, os, this.up) : wt.lookAt(os, Fe, this.up), this.quaternion.setFromRotationMatrix(wt), i && (wt.extractRotation(i.matrixWorld), me.setFromRotationMatrix(wt), this.quaternion.premultiply(me.invert()));
  }
  add(t) {
    if (arguments.length > 1) {
      for (let e = 0; e < arguments.length; e++)
        this.add(arguments[e]);
      return this;
    }
    return t === this ? (console.error("THREE.Object3D.add: object can't be added as a child of itself.", t), this) : (t && t.isObject3D ? (t.removeFromParent(), t.parent = this, this.children.push(t), t.dispatchEvent(ka), fe.child = t, this.dispatchEvent(fe), fe.child = null) : console.error("THREE.Object3D.add: object not an instance of THREE.Object3D.", t), this);
  }
  remove(t) {
    if (arguments.length > 1) {
      for (let s = 0; s < arguments.length; s++)
        this.remove(arguments[s]);
      return this;
    }
    const e = this.children.indexOf(t);
    return e !== -1 && (t.parent = null, this.children.splice(e, 1), t.dispatchEvent(Qh), si.child = t, this.dispatchEvent(si), si.child = null), this;
  }
  removeFromParent() {
    const t = this.parent;
    return t !== null && t.remove(this), this;
  }
  clear() {
    return this.remove(...this.children);
  }
  attach(t) {
    return this.updateWorldMatrix(!0, !1), wt.copy(this.matrixWorld).invert(), t.parent !== null && (t.parent.updateWorldMatrix(!0, !1), wt.multiply(t.parent.matrixWorld)), t.applyMatrix4(wt), t.removeFromParent(), t.parent = this, this.children.push(t), t.updateWorldMatrix(!1, !0), t.dispatchEvent(ka), fe.child = t, this.dispatchEvent(fe), fe.child = null, this;
  }
  getObjectById(t) {
    return this.getObjectByProperty("id", t);
  }
  getObjectByName(t) {
    return this.getObjectByProperty("name", t);
  }
  getObjectByProperty(t, e) {
    if (this[t] === e) return this;
    for (let s = 0, i = this.children.length; s < i; s++) {
      const r = this.children[s].getObjectByProperty(t, e);
      if (r !== void 0)
        return r;
    }
  }
  getObjectsByProperty(t, e, s = []) {
    this[t] === e && s.push(this);
    const i = this.children;
    for (let n = 0, r = i.length; n < r; n++)
      i[n].getObjectsByProperty(t, e, s);
    return s;
  }
  getWorldPosition(t) {
    return this.updateWorldMatrix(!0, !1), t.setFromMatrixPosition(this.matrixWorld);
  }
  getWorldQuaternion(t) {
    return this.updateWorldMatrix(!0, !1), this.matrixWorld.decompose(Fe, t, Jh), t;
  }
  getWorldScale(t) {
    return this.updateWorldMatrix(!0, !1), this.matrixWorld.decompose(Fe, Dh, t), t;
  }
  getWorldDirection(t) {
    this.updateWorldMatrix(!0, !1);
    const e = this.matrixWorld.elements;
    return t.set(e[8], e[9], e[10]).normalize();
  }
  raycast() {
  }
  traverse(t) {
    t(this);
    const e = this.children;
    for (let s = 0, i = e.length; s < i; s++)
      e[s].traverse(t);
  }
  traverseVisible(t) {
    if (this.visible === !1) return;
    t(this);
    const e = this.children;
    for (let s = 0, i = e.length; s < i; s++)
      e[s].traverseVisible(t);
  }
  traverseAncestors(t) {
    const e = this.parent;
    e !== null && (t(e), e.traverseAncestors(t));
  }
  updateMatrix() {
    this.matrix.compose(this.position, this.quaternion, this.scale), this.matrixWorldNeedsUpdate = !0;
  }
  updateMatrixWorld(t) {
    this.matrixAutoUpdate && this.updateMatrix(), (this.matrixWorldNeedsUpdate || t) && (this.matrixWorldAutoUpdate === !0 && (this.parent === null ? this.matrixWorld.copy(this.matrix) : this.matrixWorld.multiplyMatrices(this.parent.matrixWorld, this.matrix)), this.matrixWorldNeedsUpdate = !1, t = !0);
    const e = this.children;
    for (let s = 0, i = e.length; s < i; s++)
      e[s].updateMatrixWorld(t);
  }
  updateWorldMatrix(t, e) {
    const s = this.parent;
    if (t === !0 && s !== null && s.updateWorldMatrix(!0, !1), this.matrixAutoUpdate && this.updateMatrix(), this.matrixWorldAutoUpdate === !0 && (this.parent === null ? this.matrixWorld.copy(this.matrix) : this.matrixWorld.multiplyMatrices(this.parent.matrixWorld, this.matrix)), e === !0) {
      const i = this.children;
      for (let n = 0, r = i.length; n < r; n++)
        i[n].updateWorldMatrix(!1, !0);
    }
  }
  toJSON(t) {
    const e = t === void 0 || typeof t == "string", s = {};
    e && (t = {
      geometries: {},
      materials: {},
      textures: {},
      images: {},
      shapes: {},
      skeletons: {},
      animations: {},
      nodes: {}
    }, s.metadata = {
      version: 4.6,
      type: "Object",
      generator: "Object3D.toJSON"
    });
    const i = {};
    i.uuid = this.uuid, i.type = this.type, this.name !== "" && (i.name = this.name), this.castShadow === !0 && (i.castShadow = !0), this.receiveShadow === !0 && (i.receiveShadow = !0), this.visible === !1 && (i.visible = !1), this.frustumCulled === !1 && (i.frustumCulled = !1), this.renderOrder !== 0 && (i.renderOrder = this.renderOrder), Object.keys(this.userData).length > 0 && (i.userData = this.userData), i.layers = this.layers.mask, i.matrix = this.matrix.toArray(), i.up = this.up.toArray(), this.matrixAutoUpdate === !1 && (i.matrixAutoUpdate = !1), this.isInstancedMesh && (i.type = "InstancedMesh", i.count = this.count, i.instanceMatrix = this.instanceMatrix.toJSON(), this.instanceColor !== null && (i.instanceColor = this.instanceColor.toJSON())), this.isBatchedMesh && (i.type = "BatchedMesh", i.perObjectFrustumCulled = this.perObjectFrustumCulled, i.sortObjects = this.sortObjects, i.drawRanges = this._drawRanges, i.reservedRanges = this._reservedRanges, i.visibility = this._visibility, i.active = this._active, i.bounds = this._bounds.map((o) => ({
      boxInitialized: o.boxInitialized,
      boxMin: o.box.min.toArray(),
      boxMax: o.box.max.toArray(),
      sphereInitialized: o.sphereInitialized,
      sphereRadius: o.sphere.radius,
      sphereCenter: o.sphere.center.toArray()
    })), i.maxInstanceCount = this._maxInstanceCount, i.maxVertexCount = this._maxVertexCount, i.maxIndexCount = this._maxIndexCount, i.geometryInitialized = this._geometryInitialized, i.geometryCount = this._geometryCount, i.matricesTexture = this._matricesTexture.toJSON(t), this._colorsTexture !== null && (i.colorsTexture = this._colorsTexture.toJSON(t)), this.boundingSphere !== null && (i.boundingSphere = {
      center: i.boundingSphere.center.toArray(),
      radius: i.boundingSphere.radius
    }), this.boundingBox !== null && (i.boundingBox = {
      min: i.boundingBox.min.toArray(),
      max: i.boundingBox.max.toArray()
    }));
    function n(o, l) {
      return o[l.uuid] === void 0 && (o[l.uuid] = l.toJSON(t)), l.uuid;
    }
    if (this.isScene)
      this.background && (this.background.isColor ? i.background = this.background.toJSON() : this.background.isTexture && (i.background = this.background.toJSON(t).uuid)), this.environment && this.environment.isTexture && this.environment.isRenderTargetTexture !== !0 && (i.environment = this.environment.toJSON(t).uuid);
    else if (this.isMesh || this.isLine || this.isPoints) {
      i.geometry = n(t.geometries, this.geometry);
      const o = this.geometry.parameters;
      if (o !== void 0 && o.shapes !== void 0) {
        const l = o.shapes;
        if (Array.isArray(l))
          for (let c = 0, h = l.length; c < h; c++) {
            const u = l[c];
            n(t.shapes, u);
          }
        else
          n(t.shapes, l);
      }
    }
    if (this.isSkinnedMesh && (i.bindMode = this.bindMode, i.bindMatrix = this.bindMatrix.toArray(), this.skeleton !== void 0 && (n(t.skeletons, this.skeleton), i.skeleton = this.skeleton.uuid)), this.material !== void 0)
      if (Array.isArray(this.material)) {
        const o = [];
        for (let l = 0, c = this.material.length; l < c; l++)
          o.push(n(t.materials, this.material[l]));
        i.material = o;
      } else
        i.material = n(t.materials, this.material);
    if (this.children.length > 0) {
      i.children = [];
      for (let o = 0; o < this.children.length; o++)
        i.children.push(this.children[o].toJSON(t).object);
    }
    if (this.animations.length > 0) {
      i.animations = [];
      for (let o = 0; o < this.animations.length; o++) {
        const l = this.animations[o];
        i.animations.push(n(t.animations, l));
      }
    }
    if (e) {
      const o = r(t.geometries), l = r(t.materials), c = r(t.textures), h = r(t.images), u = r(t.shapes), d = r(t.skeletons), p = r(t.animations), m = r(t.nodes);
      o.length > 0 && (s.geometries = o), l.length > 0 && (s.materials = l), c.length > 0 && (s.textures = c), h.length > 0 && (s.images = h), u.length > 0 && (s.shapes = u), d.length > 0 && (s.skeletons = d), p.length > 0 && (s.animations = p), m.length > 0 && (s.nodes = m);
    }
    return s.object = i, s;
    function r(o) {
      const l = [];
      for (const c in o) {
        const h = o[c];
        delete h.metadata, l.push(h);
      }
      return l;
    }
  }
  clone(t) {
    return new this.constructor().copy(this, t);
  }
  copy(t, e = !0) {
    if (this.name = t.name, this.up.copy(t.up), this.position.copy(t.position), this.rotation.order = t.rotation.order, this.quaternion.copy(t.quaternion), this.scale.copy(t.scale), this.matrix.copy(t.matrix), this.matrixWorld.copy(t.matrixWorld), this.matrixAutoUpdate = t.matrixAutoUpdate, this.matrixWorldAutoUpdate = t.matrixWorldAutoUpdate, this.matrixWorldNeedsUpdate = t.matrixWorldNeedsUpdate, this.layers.mask = t.layers.mask, this.visible = t.visible, this.castShadow = t.castShadow, this.receiveShadow = t.receiveShadow, this.frustumCulled = t.frustumCulled, this.renderOrder = t.renderOrder, this.animations = t.animations.slice(), this.userData = JSON.parse(JSON.stringify(t.userData)), e === !0)
      for (let s = 0; s < t.children.length; s++) {
        const i = t.children[s];
        this.add(i.clone());
      }
    return this;
  }
}
Rt.DEFAULT_UP = /* @__PURE__ */ new R(0, 1, 0);
Rt.DEFAULT_MATRIX_AUTO_UPDATE = !0;
Rt.DEFAULT_MATRIX_WORLD_AUTO_UPDATE = !0;
const bt = /* @__PURE__ */ new R(), vt = /* @__PURE__ */ new R(), ii = /* @__PURE__ */ new R(), Ft = /* @__PURE__ */ new R(), be = /* @__PURE__ */ new R(), ye = /* @__PURE__ */ new R(), Ta = /* @__PURE__ */ new R(), ni = /* @__PURE__ */ new R(), ri = /* @__PURE__ */ new R(), ai = /* @__PURE__ */ new R(), oi = /* @__PURE__ */ new Pe(), li = /* @__PURE__ */ new Pe(), ci = /* @__PURE__ */ new Pe();
class xt {
  constructor(t = new R(), e = new R(), s = new R()) {
    this.a = t, this.b = e, this.c = s;
  }
  static getNormal(t, e, s, i) {
    i.subVectors(s, e), bt.subVectors(t, e), i.cross(bt);
    const n = i.lengthSq();
    return n > 0 ? i.multiplyScalar(1 / Math.sqrt(n)) : i.set(0, 0, 0);
  }
  // static/instance method to calculate barycentric coordinates
  // based on: http://www.blackpawn.com/texts/pointinpoly/default.html
  static getBarycoord(t, e, s, i, n) {
    bt.subVectors(i, e), vt.subVectors(s, e), ii.subVectors(t, e);
    const r = bt.dot(bt), o = bt.dot(vt), l = bt.dot(ii), c = vt.dot(vt), h = vt.dot(ii), u = r * c - o * o;
    if (u === 0)
      return n.set(0, 0, 0), null;
    const d = 1 / u, p = (c * l - o * h) * d, m = (r * h - o * l) * d;
    return n.set(1 - p - m, m, p);
  }
  static containsPoint(t, e, s, i) {
    return this.getBarycoord(t, e, s, i, Ft) === null ? !1 : Ft.x >= 0 && Ft.y >= 0 && Ft.x + Ft.y <= 1;
  }
  static getInterpolation(t, e, s, i, n, r, o, l) {
    return this.getBarycoord(t, e, s, i, Ft) === null ? (l.x = 0, l.y = 0, "z" in l && (l.z = 0), "w" in l && (l.w = 0), null) : (l.setScalar(0), l.addScaledVector(n, Ft.x), l.addScaledVector(r, Ft.y), l.addScaledVector(o, Ft.z), l);
  }
  static getInterpolatedAttribute(t, e, s, i, n, r) {
    return oi.setScalar(0), li.setScalar(0), ci.setScalar(0), oi.fromBufferAttribute(t, e), li.fromBufferAttribute(t, s), ci.fromBufferAttribute(t, i), r.setScalar(0), r.addScaledVector(oi, n.x), r.addScaledVector(li, n.y), r.addScaledVector(ci, n.z), r;
  }
  static isFrontFacing(t, e, s, i) {
    return bt.subVectors(s, e), vt.subVectors(t, e), bt.cross(vt).dot(i) < 0;
  }
  set(t, e, s) {
    return this.a.copy(t), this.b.copy(e), this.c.copy(s), this;
  }
  setFromPointsAndIndices(t, e, s, i) {
    return this.a.copy(t[e]), this.b.copy(t[s]), this.c.copy(t[i]), this;
  }
  setFromAttributeAndIndices(t, e, s, i) {
    return this.a.fromBufferAttribute(t, e), this.b.fromBufferAttribute(t, s), this.c.fromBufferAttribute(t, i), this;
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    return this.a.copy(t.a), this.b.copy(t.b), this.c.copy(t.c), this;
  }
  getArea() {
    return bt.subVectors(this.c, this.b), vt.subVectors(this.a, this.b), bt.cross(vt).length() * 0.5;
  }
  getMidpoint(t) {
    return t.addVectors(this.a, this.b).add(this.c).multiplyScalar(1 / 3);
  }
  getNormal(t) {
    return xt.getNormal(this.a, this.b, this.c, t);
  }
  getPlane(t) {
    return t.setFromCoplanarPoints(this.a, this.b, this.c);
  }
  getBarycoord(t, e) {
    return xt.getBarycoord(t, this.a, this.b, this.c, e);
  }
  getInterpolation(t, e, s, i, n) {
    return xt.getInterpolation(t, this.a, this.b, this.c, e, s, i, n);
  }
  containsPoint(t) {
    return xt.containsPoint(t, this.a, this.b, this.c);
  }
  isFrontFacing(t) {
    return xt.isFrontFacing(this.a, this.b, this.c, t);
  }
  intersectsBox(t) {
    return t.intersectsTriangle(this);
  }
  closestPointToPoint(t, e) {
    const s = this.a, i = this.b, n = this.c;
    let r, o;
    be.subVectors(i, s), ye.subVectors(n, s), ni.subVectors(t, s);
    const l = be.dot(ni), c = ye.dot(ni);
    if (l <= 0 && c <= 0)
      return e.copy(s);
    ri.subVectors(t, i);
    const h = be.dot(ri), u = ye.dot(ri);
    if (h >= 0 && u <= h)
      return e.copy(i);
    const d = l * u - h * c;
    if (d <= 0 && l >= 0 && h <= 0)
      return r = l / (l - h), e.copy(s).addScaledVector(be, r);
    ai.subVectors(t, n);
    const p = be.dot(ai), m = ye.dot(ai);
    if (m >= 0 && p <= m)
      return e.copy(n);
    const f = p * c - l * m;
    if (f <= 0 && c >= 0 && m <= 0)
      return o = c / (c - m), e.copy(s).addScaledVector(ye, o);
    const b = h * m - p * u;
    if (b <= 0 && u - h >= 0 && p - m >= 0)
      return Ta.subVectors(n, i), o = (u - h) / (u - h + (p - m)), e.copy(i).addScaledVector(Ta, o);
    const y = 1 / (b + f + d);
    return r = f * y, o = d * y, e.copy(s).addScaledVector(be, r).addScaledVector(ye, o);
  }
  equals(t) {
    return t.a.equals(this.a) && t.b.equals(this.b) && t.c.equals(this.c);
  }
}
const Sc = {
  aliceblue: 15792383,
  antiquewhite: 16444375,
  aqua: 65535,
  aquamarine: 8388564,
  azure: 15794175,
  beige: 16119260,
  bisque: 16770244,
  black: 0,
  blanchedalmond: 16772045,
  blue: 255,
  blueviolet: 9055202,
  brown: 10824234,
  burlywood: 14596231,
  cadetblue: 6266528,
  chartreuse: 8388352,
  chocolate: 13789470,
  coral: 16744272,
  cornflowerblue: 6591981,
  cornsilk: 16775388,
  crimson: 14423100,
  cyan: 65535,
  darkblue: 139,
  darkcyan: 35723,
  darkgoldenrod: 12092939,
  darkgray: 11119017,
  darkgreen: 25600,
  darkgrey: 11119017,
  darkkhaki: 12433259,
  darkmagenta: 9109643,
  darkolivegreen: 5597999,
  darkorange: 16747520,
  darkorchid: 10040012,
  darkred: 9109504,
  darksalmon: 15308410,
  darkseagreen: 9419919,
  darkslateblue: 4734347,
  darkslategray: 3100495,
  darkslategrey: 3100495,
  darkturquoise: 52945,
  darkviolet: 9699539,
  deeppink: 16716947,
  deepskyblue: 49151,
  dimgray: 6908265,
  dimgrey: 6908265,
  dodgerblue: 2003199,
  firebrick: 11674146,
  floralwhite: 16775920,
  forestgreen: 2263842,
  fuchsia: 16711935,
  gainsboro: 14474460,
  ghostwhite: 16316671,
  gold: 16766720,
  goldenrod: 14329120,
  gray: 8421504,
  green: 32768,
  greenyellow: 11403055,
  grey: 8421504,
  honeydew: 15794160,
  hotpink: 16738740,
  indianred: 13458524,
  indigo: 4915330,
  ivory: 16777200,
  khaki: 15787660,
  lavender: 15132410,
  lavenderblush: 16773365,
  lawngreen: 8190976,
  lemonchiffon: 16775885,
  lightblue: 11393254,
  lightcoral: 15761536,
  lightcyan: 14745599,
  lightgoldenrodyellow: 16448210,
  lightgray: 13882323,
  lightgreen: 9498256,
  lightgrey: 13882323,
  lightpink: 16758465,
  lightsalmon: 16752762,
  lightseagreen: 2142890,
  lightskyblue: 8900346,
  lightslategray: 7833753,
  lightslategrey: 7833753,
  lightsteelblue: 11584734,
  lightyellow: 16777184,
  lime: 65280,
  limegreen: 3329330,
  linen: 16445670,
  magenta: 16711935,
  maroon: 8388608,
  mediumaquamarine: 6737322,
  mediumblue: 205,
  mediumorchid: 12211667,
  mediumpurple: 9662683,
  mediumseagreen: 3978097,
  mediumslateblue: 8087790,
  mediumspringgreen: 64154,
  mediumturquoise: 4772300,
  mediumvioletred: 13047173,
  midnightblue: 1644912,
  mintcream: 16121850,
  mistyrose: 16770273,
  moccasin: 16770229,
  navajowhite: 16768685,
  navy: 128,
  oldlace: 16643558,
  olive: 8421376,
  olivedrab: 7048739,
  orange: 16753920,
  orangered: 16729344,
  orchid: 14315734,
  palegoldenrod: 15657130,
  palegreen: 10025880,
  paleturquoise: 11529966,
  palevioletred: 14381203,
  papayawhip: 16773077,
  peachpuff: 16767673,
  peru: 13468991,
  pink: 16761035,
  plum: 14524637,
  powderblue: 11591910,
  purple: 8388736,
  rebeccapurple: 6697881,
  red: 16711680,
  rosybrown: 12357519,
  royalblue: 4286945,
  saddlebrown: 9127187,
  salmon: 16416882,
  sandybrown: 16032864,
  seagreen: 3050327,
  seashell: 16774638,
  sienna: 10506797,
  silver: 12632256,
  skyblue: 8900331,
  slateblue: 6970061,
  slategray: 7372944,
  slategrey: 7372944,
  snow: 16775930,
  springgreen: 65407,
  steelblue: 4620980,
  tan: 13808780,
  teal: 32896,
  thistle: 14204888,
  tomato: 16737095,
  turquoise: 4251856,
  violet: 15631086,
  wheat: 16113331,
  white: 16777215,
  whitesmoke: 16119285,
  yellow: 16776960,
  yellowgreen: 10145074
}, Pt = { h: 0, s: 0, l: 0 }, ls = { h: 0, s: 0, l: 0 };
function hi(a, t, e) {
  return e < 0 && (e += 1), e > 1 && (e -= 1), e < 1 / 6 ? a + (t - a) * 6 * e : e < 1 / 2 ? t : e < 2 / 3 ? a + (t - a) * 6 * (2 / 3 - e) : a;
}
class Jt {
  constructor(t, e, s) {
    return this.isColor = !0, this.r = 1, this.g = 1, this.b = 1, this.set(t, e, s);
  }
  set(t, e, s) {
    if (e === void 0 && s === void 0) {
      const i = t;
      i && i.isColor ? this.copy(i) : typeof i == "number" ? this.setHex(i) : typeof i == "string" && this.setStyle(i);
    } else
      this.setRGB(t, e, s);
    return this;
  }
  setScalar(t) {
    return this.r = t, this.g = t, this.b = t, this;
  }
  setHex(t, e = yt) {
    return t = Math.floor(t), this.r = (t >> 16 & 255) / 255, this.g = (t >> 8 & 255) / 255, this.b = (t & 255) / 255, pt.toWorkingColorSpace(this, e), this;
  }
  setRGB(t, e, s, i = pt.workingColorSpace) {
    return this.r = t, this.g = e, this.b = s, pt.toWorkingColorSpace(this, i), this;
  }
  setHSL(t, e, s, i = pt.workingColorSpace) {
    if (t = Ch(t, 1), e = T(e, 0, 1), s = T(s, 0, 1), e === 0)
      this.r = this.g = this.b = s;
    else {
      const n = s <= 0.5 ? s * (1 + e) : s + e - s * e, r = 2 * s - n;
      this.r = hi(r, n, t + 1 / 3), this.g = hi(r, n, t), this.b = hi(r, n, t - 1 / 3);
    }
    return pt.toWorkingColorSpace(this, i), this;
  }
  setStyle(t, e = yt) {
    function s(n) {
      n !== void 0 && parseFloat(n) < 1 && console.warn("THREE.Color: Alpha component of " + t + " will be ignored.");
    }
    let i;
    if (i = /^(\w+)\(([^\)]*)\)/.exec(t)) {
      let n;
      const r = i[1], o = i[2];
      switch (r) {
        case "rgb":
        case "rgba":
          if (n = /^\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d*\.?\d+)\s*)?$/.exec(o))
            return s(n[4]), this.setRGB(
              Math.min(255, parseInt(n[1], 10)) / 255,
              Math.min(255, parseInt(n[2], 10)) / 255,
              Math.min(255, parseInt(n[3], 10)) / 255,
              e
            );
          if (n = /^\s*(\d+)\%\s*,\s*(\d+)\%\s*,\s*(\d+)\%\s*(?:,\s*(\d*\.?\d+)\s*)?$/.exec(o))
            return s(n[4]), this.setRGB(
              Math.min(100, parseInt(n[1], 10)) / 100,
              Math.min(100, parseInt(n[2], 10)) / 100,
              Math.min(100, parseInt(n[3], 10)) / 100,
              e
            );
          break;
        case "hsl":
        case "hsla":
          if (n = /^\s*(\d*\.?\d+)\s*,\s*(\d*\.?\d+)\%\s*,\s*(\d*\.?\d+)\%\s*(?:,\s*(\d*\.?\d+)\s*)?$/.exec(o))
            return s(n[4]), this.setHSL(
              parseFloat(n[1]) / 360,
              parseFloat(n[2]) / 100,
              parseFloat(n[3]) / 100,
              e
            );
          break;
        default:
          console.warn("THREE.Color: Unknown color model " + t);
      }
    } else if (i = /^\#([A-Fa-f\d]+)$/.exec(t)) {
      const n = i[1], r = n.length;
      if (r === 3)
        return this.setRGB(
          parseInt(n.charAt(0), 16) / 15,
          parseInt(n.charAt(1), 16) / 15,
          parseInt(n.charAt(2), 16) / 15,
          e
        );
      if (r === 6)
        return this.setHex(parseInt(n, 16), e);
      console.warn("THREE.Color: Invalid hex color " + t);
    } else if (t && t.length > 0)
      return this.setColorName(t, e);
    return this;
  }
  setColorName(t, e = yt) {
    const s = Sc[t.toLowerCase()];
    return s !== void 0 ? this.setHex(s, e) : console.warn("THREE.Color: Unknown color " + t), this;
  }
  clone() {
    return new this.constructor(this.r, this.g, this.b);
  }
  copy(t) {
    return this.r = t.r, this.g = t.g, this.b = t.b, this;
  }
  copySRGBToLinear(t) {
    return this.r = Wt(t.r), this.g = Wt(t.g), this.b = Wt(t.b), this;
  }
  copyLinearToSRGB(t) {
    return this.r = Ze(t.r), this.g = Ze(t.g), this.b = Ze(t.b), this;
  }
  convertSRGBToLinear() {
    return this.copySRGBToLinear(this), this;
  }
  convertLinearToSRGB() {
    return this.copyLinearToSRGB(this), this;
  }
  getHex(t = yt) {
    return pt.fromWorkingColorSpace(j.copy(this), t), Math.round(T(j.r * 255, 0, 255)) * 65536 + Math.round(T(j.g * 255, 0, 255)) * 256 + Math.round(T(j.b * 255, 0, 255));
  }
  getHexString(t = yt) {
    return ("000000" + this.getHex(t).toString(16)).slice(-6);
  }
  getHSL(t, e = pt.workingColorSpace) {
    pt.fromWorkingColorSpace(j.copy(this), e);
    const s = j.r, i = j.g, n = j.b, r = Math.max(s, i, n), o = Math.min(s, i, n);
    let l, c;
    const h = (o + r) / 2;
    if (o === r)
      l = 0, c = 0;
    else {
      const u = r - o;
      switch (c = h <= 0.5 ? u / (r + o) : u / (2 - r - o), r) {
        case s:
          l = (i - n) / u + (i < n ? 6 : 0);
          break;
        case i:
          l = (n - s) / u + 2;
          break;
        case n:
          l = (s - i) / u + 4;
          break;
      }
      l /= 6;
    }
    return t.h = l, t.s = c, t.l = h, t;
  }
  getRGB(t, e = pt.workingColorSpace) {
    return pt.fromWorkingColorSpace(j.copy(this), e), t.r = j.r, t.g = j.g, t.b = j.b, t;
  }
  getStyle(t = yt) {
    pt.fromWorkingColorSpace(j.copy(this), t);
    const e = j.r, s = j.g, i = j.b;
    return t !== yt ? `color(${t} ${e.toFixed(3)} ${s.toFixed(3)} ${i.toFixed(3)})` : `rgb(${Math.round(e * 255)},${Math.round(s * 255)},${Math.round(i * 255)})`;
  }
  offsetHSL(t, e, s) {
    return this.getHSL(Pt), this.setHSL(Pt.h + t, Pt.s + e, Pt.l + s);
  }
  add(t) {
    return this.r += t.r, this.g += t.g, this.b += t.b, this;
  }
  addColors(t, e) {
    return this.r = t.r + e.r, this.g = t.g + e.g, this.b = t.b + e.b, this;
  }
  addScalar(t) {
    return this.r += t, this.g += t, this.b += t, this;
  }
  sub(t) {
    return this.r = Math.max(0, this.r - t.r), this.g = Math.max(0, this.g - t.g), this.b = Math.max(0, this.b - t.b), this;
  }
  multiply(t) {
    return this.r *= t.r, this.g *= t.g, this.b *= t.b, this;
  }
  multiplyScalar(t) {
    return this.r *= t, this.g *= t, this.b *= t, this;
  }
  lerp(t, e) {
    return this.r += (t.r - this.r) * e, this.g += (t.g - this.g) * e, this.b += (t.b - this.b) * e, this;
  }
  lerpColors(t, e, s) {
    return this.r = t.r + (e.r - t.r) * s, this.g = t.g + (e.g - t.g) * s, this.b = t.b + (e.b - t.b) * s, this;
  }
  lerpHSL(t, e) {
    this.getHSL(Pt), t.getHSL(ls);
    const s = Js(Pt.h, ls.h, e), i = Js(Pt.s, ls.s, e), n = Js(Pt.l, ls.l, e);
    return this.setHSL(s, i, n), this;
  }
  setFromVector3(t) {
    return this.r = t.x, this.g = t.y, this.b = t.z, this;
  }
  applyMatrix3(t) {
    const e = this.r, s = this.g, i = this.b, n = t.elements;
    return this.r = n[0] * e + n[3] * s + n[6] * i, this.g = n[1] * e + n[4] * s + n[7] * i, this.b = n[2] * e + n[5] * s + n[8] * i, this;
  }
  equals(t) {
    return t.r === this.r && t.g === this.g && t.b === this.b;
  }
  fromArray(t, e = 0) {
    return this.r = t[e], this.g = t[e + 1], this.b = t[e + 2], this;
  }
  toArray(t = [], e = 0) {
    return t[e] = this.r, t[e + 1] = this.g, t[e + 2] = this.b, t;
  }
  fromBufferAttribute(t, e) {
    return this.r = t.getX(e), this.g = t.getY(e), this.b = t.getZ(e), this;
  }
  toJSON() {
    return this.getHex();
  }
  *[Symbol.iterator]() {
    yield this.r, yield this.g, yield this.b;
  }
}
const j = /* @__PURE__ */ new Jt();
Jt.NAMES = Sc;
let jh = 0;
class Ur extends Fs {
  constructor() {
    super(), this.isMaterial = !0, Object.defineProperty(this, "id", { value: jh++ }), this.uuid = Ve(), this.name = "", this.type = "Material", this.blending = 1, this.side = 0, this.vertexColors = !1, this.opacity = 1, this.transparent = !1, this.alphaHash = !1, this.blendSrc = 204, this.blendDst = 205, this.blendEquation = 100, this.blendSrcAlpha = null, this.blendDstAlpha = null, this.blendEquationAlpha = null, this.blendColor = new Jt(0, 0, 0), this.blendAlpha = 0, this.depthFunc = 3, this.depthTest = !0, this.depthWrite = !0, this.stencilWriteMask = 255, this.stencilFunc = 519, this.stencilRef = 0, this.stencilFuncMask = 255, this.stencilFail = 7680, this.stencilZFail = 7680, this.stencilZPass = 7680, this.stencilWrite = !1, this.clippingPlanes = null, this.clipIntersection = !1, this.clipShadows = !1, this.shadowSide = null, this.colorWrite = !0, this.precision = null, this.polygonOffset = !1, this.polygonOffsetFactor = 0, this.polygonOffsetUnits = 0, this.dithering = !1, this.alphaToCoverage = !1, this.premultipliedAlpha = !1, this.forceSinglePass = !1, this.visible = !0, this.toneMapped = !0, this.userData = {}, this.version = 0, this._alphaTest = 0;
  }
  get alphaTest() {
    return this._alphaTest;
  }
  set alphaTest(t) {
    this._alphaTest > 0 != t > 0 && this.version++, this._alphaTest = t;
  }
  // onBeforeRender and onBeforeCompile only supported in WebGLRenderer
  onBeforeRender() {
  }
  onBeforeCompile() {
  }
  customProgramCacheKey() {
    return this.onBeforeCompile.toString();
  }
  setValues(t) {
    if (t !== void 0)
      for (const e in t) {
        const s = t[e];
        if (s === void 0) {
          console.warn(`THREE.Material: parameter '${e}' has value of undefined.`);
          continue;
        }
        const i = this[e];
        if (i === void 0) {
          console.warn(`THREE.Material: '${e}' is not a property of THREE.${this.type}.`);
          continue;
        }
        i && i.isColor ? i.set(s) : i && i.isVector3 && s && s.isVector3 ? i.copy(s) : this[e] = s;
      }
  }
  toJSON(t) {
    const e = t === void 0 || typeof t == "string";
    e && (t = {
      textures: {},
      images: {}
    });
    const s = {
      metadata: {
        version: 4.6,
        type: "Material",
        generator: "Material.toJSON"
      }
    };
    s.uuid = this.uuid, s.type = this.type, this.name !== "" && (s.name = this.name), this.color && this.color.isColor && (s.color = this.color.getHex()), this.roughness !== void 0 && (s.roughness = this.roughness), this.metalness !== void 0 && (s.metalness = this.metalness), this.sheen !== void 0 && (s.sheen = this.sheen), this.sheenColor && this.sheenColor.isColor && (s.sheenColor = this.sheenColor.getHex()), this.sheenRoughness !== void 0 && (s.sheenRoughness = this.sheenRoughness), this.emissive && this.emissive.isColor && (s.emissive = this.emissive.getHex()), this.emissiveIntensity !== void 0 && this.emissiveIntensity !== 1 && (s.emissiveIntensity = this.emissiveIntensity), this.specular && this.specular.isColor && (s.specular = this.specular.getHex()), this.specularIntensity !== void 0 && (s.specularIntensity = this.specularIntensity), this.specularColor && this.specularColor.isColor && (s.specularColor = this.specularColor.getHex()), this.shininess !== void 0 && (s.shininess = this.shininess), this.clearcoat !== void 0 && (s.clearcoat = this.clearcoat), this.clearcoatRoughness !== void 0 && (s.clearcoatRoughness = this.clearcoatRoughness), this.clearcoatMap && this.clearcoatMap.isTexture && (s.clearcoatMap = this.clearcoatMap.toJSON(t).uuid), this.clearcoatRoughnessMap && this.clearcoatRoughnessMap.isTexture && (s.clearcoatRoughnessMap = this.clearcoatRoughnessMap.toJSON(t).uuid), this.clearcoatNormalMap && this.clearcoatNormalMap.isTexture && (s.clearcoatNormalMap = this.clearcoatNormalMap.toJSON(t).uuid, s.clearcoatNormalScale = this.clearcoatNormalScale.toArray()), this.dispersion !== void 0 && (s.dispersion = this.dispersion), this.iridescence !== void 0 && (s.iridescence = this.iridescence), this.iridescenceIOR !== void 0 && (s.iridescenceIOR = this.iridescenceIOR), this.iridescenceThicknessRange !== void 0 && (s.iridescenceThicknessRange = this.iridescenceThicknessRange), this.iridescenceMap && this.iridescenceMap.isTexture && (s.iridescenceMap = this.iridescenceMap.toJSON(t).uuid), this.iridescenceThicknessMap && this.iridescenceThicknessMap.isTexture && (s.iridescenceThicknessMap = this.iridescenceThicknessMap.toJSON(t).uuid), this.anisotropy !== void 0 && (s.anisotropy = this.anisotropy), this.anisotropyRotation !== void 0 && (s.anisotropyRotation = this.anisotropyRotation), this.anisotropyMap && this.anisotropyMap.isTexture && (s.anisotropyMap = this.anisotropyMap.toJSON(t).uuid), this.map && this.map.isTexture && (s.map = this.map.toJSON(t).uuid), this.matcap && this.matcap.isTexture && (s.matcap = this.matcap.toJSON(t).uuid), this.alphaMap && this.alphaMap.isTexture && (s.alphaMap = this.alphaMap.toJSON(t).uuid), this.lightMap && this.lightMap.isTexture && (s.lightMap = this.lightMap.toJSON(t).uuid, s.lightMapIntensity = this.lightMapIntensity), this.aoMap && this.aoMap.isTexture && (s.aoMap = this.aoMap.toJSON(t).uuid, s.aoMapIntensity = this.aoMapIntensity), this.bumpMap && this.bumpMap.isTexture && (s.bumpMap = this.bumpMap.toJSON(t).uuid, s.bumpScale = this.bumpScale), this.normalMap && this.normalMap.isTexture && (s.normalMap = this.normalMap.toJSON(t).uuid, s.normalMapType = this.normalMapType, s.normalScale = this.normalScale.toArray()), this.displacementMap && this.displacementMap.isTexture && (s.displacementMap = this.displacementMap.toJSON(t).uuid, s.displacementScale = this.displacementScale, s.displacementBias = this.displacementBias), this.roughnessMap && this.roughnessMap.isTexture && (s.roughnessMap = this.roughnessMap.toJSON(t).uuid), this.metalnessMap && this.metalnessMap.isTexture && (s.metalnessMap = this.metalnessMap.toJSON(t).uuid), this.emissiveMap && this.emissiveMap.isTexture && (s.emissiveMap = this.emissiveMap.toJSON(t).uuid), this.specularMap && this.specularMap.isTexture && (s.specularMap = this.specularMap.toJSON(t).uuid), this.specularIntensityMap && this.specularIntensityMap.isTexture && (s.specularIntensityMap = this.specularIntensityMap.toJSON(t).uuid), this.specularColorMap && this.specularColorMap.isTexture && (s.specularColorMap = this.specularColorMap.toJSON(t).uuid), this.envMap && this.envMap.isTexture && (s.envMap = this.envMap.toJSON(t).uuid, this.combine !== void 0 && (s.combine = this.combine)), this.envMapRotation !== void 0 && (s.envMapRotation = this.envMapRotation.toArray()), this.envMapIntensity !== void 0 && (s.envMapIntensity = this.envMapIntensity), this.reflectivity !== void 0 && (s.reflectivity = this.reflectivity), this.refractionRatio !== void 0 && (s.refractionRatio = this.refractionRatio), this.gradientMap && this.gradientMap.isTexture && (s.gradientMap = this.gradientMap.toJSON(t).uuid), this.transmission !== void 0 && (s.transmission = this.transmission), this.transmissionMap && this.transmissionMap.isTexture && (s.transmissionMap = this.transmissionMap.toJSON(t).uuid), this.thickness !== void 0 && (s.thickness = this.thickness), this.thicknessMap && this.thicknessMap.isTexture && (s.thicknessMap = this.thicknessMap.toJSON(t).uuid), this.attenuationDistance !== void 0 && this.attenuationDistance !== 1 / 0 && (s.attenuationDistance = this.attenuationDistance), this.attenuationColor !== void 0 && (s.attenuationColor = this.attenuationColor.getHex()), this.size !== void 0 && (s.size = this.size), this.shadowSide !== null && (s.shadowSide = this.shadowSide), this.sizeAttenuation !== void 0 && (s.sizeAttenuation = this.sizeAttenuation), this.blending !== 1 && (s.blending = this.blending), this.side !== 0 && (s.side = this.side), this.vertexColors === !0 && (s.vertexColors = !0), this.opacity < 1 && (s.opacity = this.opacity), this.transparent === !0 && (s.transparent = !0), this.blendSrc !== 204 && (s.blendSrc = this.blendSrc), this.blendDst !== 205 && (s.blendDst = this.blendDst), this.blendEquation !== 100 && (s.blendEquation = this.blendEquation), this.blendSrcAlpha !== null && (s.blendSrcAlpha = this.blendSrcAlpha), this.blendDstAlpha !== null && (s.blendDstAlpha = this.blendDstAlpha), this.blendEquationAlpha !== null && (s.blendEquationAlpha = this.blendEquationAlpha), this.blendColor && this.blendColor.isColor && (s.blendColor = this.blendColor.getHex()), this.blendAlpha !== 0 && (s.blendAlpha = this.blendAlpha), this.depthFunc !== 3 && (s.depthFunc = this.depthFunc), this.depthTest === !1 && (s.depthTest = this.depthTest), this.depthWrite === !1 && (s.depthWrite = this.depthWrite), this.colorWrite === !1 && (s.colorWrite = this.colorWrite), this.stencilWriteMask !== 255 && (s.stencilWriteMask = this.stencilWriteMask), this.stencilFunc !== 519 && (s.stencilFunc = this.stencilFunc), this.stencilRef !== 0 && (s.stencilRef = this.stencilRef), this.stencilFuncMask !== 255 && (s.stencilFuncMask = this.stencilFuncMask), this.stencilFail !== 7680 && (s.stencilFail = this.stencilFail), this.stencilZFail !== 7680 && (s.stencilZFail = this.stencilZFail), this.stencilZPass !== 7680 && (s.stencilZPass = this.stencilZPass), this.stencilWrite === !0 && (s.stencilWrite = this.stencilWrite), this.rotation !== void 0 && this.rotation !== 0 && (s.rotation = this.rotation), this.polygonOffset === !0 && (s.polygonOffset = !0), this.polygonOffsetFactor !== 0 && (s.polygonOffsetFactor = this.polygonOffsetFactor), this.polygonOffsetUnits !== 0 && (s.polygonOffsetUnits = this.polygonOffsetUnits), this.linewidth !== void 0 && this.linewidth !== 1 && (s.linewidth = this.linewidth), this.dashSize !== void 0 && (s.dashSize = this.dashSize), this.gapSize !== void 0 && (s.gapSize = this.gapSize), this.scale !== void 0 && (s.scale = this.scale), this.dithering === !0 && (s.dithering = !0), this.alphaTest > 0 && (s.alphaTest = this.alphaTest), this.alphaHash === !0 && (s.alphaHash = !0), this.alphaToCoverage === !0 && (s.alphaToCoverage = !0), this.premultipliedAlpha === !0 && (s.premultipliedAlpha = !0), this.forceSinglePass === !0 && (s.forceSinglePass = !0), this.wireframe === !0 && (s.wireframe = !0), this.wireframeLinewidth > 1 && (s.wireframeLinewidth = this.wireframeLinewidth), this.wireframeLinecap !== "round" && (s.wireframeLinecap = this.wireframeLinecap), this.wireframeLinejoin !== "round" && (s.wireframeLinejoin = this.wireframeLinejoin), this.flatShading === !0 && (s.flatShading = !0), this.visible === !1 && (s.visible = !1), this.toneMapped === !1 && (s.toneMapped = !1), this.fog === !1 && (s.fog = !1), Object.keys(this.userData).length > 0 && (s.userData = this.userData);
    function i(n) {
      const r = [];
      for (const o in n) {
        const l = n[o];
        delete l.metadata, r.push(l);
      }
      return r;
    }
    if (e) {
      const n = i(t.textures), r = i(t.images);
      n.length > 0 && (s.textures = n), r.length > 0 && (s.images = r);
    }
    return s;
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    this.name = t.name, this.blending = t.blending, this.side = t.side, this.vertexColors = t.vertexColors, this.opacity = t.opacity, this.transparent = t.transparent, this.blendSrc = t.blendSrc, this.blendDst = t.blendDst, this.blendEquation = t.blendEquation, this.blendSrcAlpha = t.blendSrcAlpha, this.blendDstAlpha = t.blendDstAlpha, this.blendEquationAlpha = t.blendEquationAlpha, this.blendColor.copy(t.blendColor), this.blendAlpha = t.blendAlpha, this.depthFunc = t.depthFunc, this.depthTest = t.depthTest, this.depthWrite = t.depthWrite, this.stencilWriteMask = t.stencilWriteMask, this.stencilFunc = t.stencilFunc, this.stencilRef = t.stencilRef, this.stencilFuncMask = t.stencilFuncMask, this.stencilFail = t.stencilFail, this.stencilZFail = t.stencilZFail, this.stencilZPass = t.stencilZPass, this.stencilWrite = t.stencilWrite;
    const e = t.clippingPlanes;
    let s = null;
    if (e !== null) {
      const i = e.length;
      s = new Array(i);
      for (let n = 0; n !== i; ++n)
        s[n] = e[n].clone();
    }
    return this.clippingPlanes = s, this.clipIntersection = t.clipIntersection, this.clipShadows = t.clipShadows, this.shadowSide = t.shadowSide, this.colorWrite = t.colorWrite, this.precision = t.precision, this.polygonOffset = t.polygonOffset, this.polygonOffsetFactor = t.polygonOffsetFactor, this.polygonOffsetUnits = t.polygonOffsetUnits, this.dithering = t.dithering, this.alphaTest = t.alphaTest, this.alphaHash = t.alphaHash, this.alphaToCoverage = t.alphaToCoverage, this.premultipliedAlpha = t.premultipliedAlpha, this.forceSinglePass = t.forceSinglePass, this.visible = t.visible, this.toneMapped = t.toneMapped, this.userData = JSON.parse(JSON.stringify(t.userData)), this;
  }
  dispose() {
    this.dispatchEvent({ type: "dispose" });
  }
  set needsUpdate(t) {
    t === !0 && this.version++;
  }
  onBuild() {
    console.warn("Material: onBuild() has been removed.");
  }
}
class ks extends Ur {
  constructor(t) {
    super(), this.isMeshBasicMaterial = !0, this.type = "MeshBasicMaterial", this.color = new Jt(16777215), this.map = null, this.lightMap = null, this.lightMapIntensity = 1, this.aoMap = null, this.aoMapIntensity = 1, this.specularMap = null, this.alphaMap = null, this.envMap = null, this.envMapRotation = new Me(), this.combine = 0, this.reflectivity = 1, this.refractionRatio = 0.98, this.wireframe = !1, this.wireframeLinewidth = 1, this.wireframeLinecap = "round", this.wireframeLinejoin = "round", this.fog = !0, this.setValues(t);
  }
  copy(t) {
    return super.copy(t), this.color.copy(t.color), this.map = t.map, this.lightMap = t.lightMap, this.lightMapIntensity = t.lightMapIntensity, this.aoMap = t.aoMap, this.aoMapIntensity = t.aoMapIntensity, this.specularMap = t.specularMap, this.alphaMap = t.alphaMap, this.envMap = t.envMap, this.envMapRotation.copy(t.envMapRotation), this.combine = t.combine, this.reflectivity = t.reflectivity, this.refractionRatio = t.refractionRatio, this.wireframe = t.wireframe, this.wireframeLinewidth = t.wireframeLinewidth, this.wireframeLinecap = t.wireframeLinecap, this.wireframeLinejoin = t.wireframeLinejoin, this.fog = t.fog, this;
  }
}
const Y = /* @__PURE__ */ new R(), cs = /* @__PURE__ */ new v();
class q {
  constructor(t, e, s = !1) {
    if (Array.isArray(t))
      throw new TypeError("THREE.BufferAttribute: array should be a Typed Array.");
    this.isBufferAttribute = !0, this.name = "", this.array = t, this.itemSize = e, this.count = t !== void 0 ? t.length / e : 0, this.normalized = s, this.usage = 35044, this.updateRanges = [], this.gpuType = 1015, this.version = 0;
  }
  onUploadCallback() {
  }
  set needsUpdate(t) {
    t === !0 && this.version++;
  }
  setUsage(t) {
    return this.usage = t, this;
  }
  addUpdateRange(t, e) {
    this.updateRanges.push({ start: t, count: e });
  }
  clearUpdateRanges() {
    this.updateRanges.length = 0;
  }
  copy(t) {
    return this.name = t.name, this.array = new t.array.constructor(t.array), this.itemSize = t.itemSize, this.count = t.count, this.normalized = t.normalized, this.usage = t.usage, this.gpuType = t.gpuType, this;
  }
  copyAt(t, e, s) {
    t *= this.itemSize, s *= e.itemSize;
    for (let i = 0, n = this.itemSize; i < n; i++)
      this.array[t + i] = e.array[s + i];
    return this;
  }
  copyArray(t) {
    return this.array.set(t), this;
  }
  applyMatrix3(t) {
    if (this.itemSize === 2)
      for (let e = 0, s = this.count; e < s; e++)
        cs.fromBufferAttribute(this, e), cs.applyMatrix3(t), this.setXY(e, cs.x, cs.y);
    else if (this.itemSize === 3)
      for (let e = 0, s = this.count; e < s; e++)
        Y.fromBufferAttribute(this, e), Y.applyMatrix3(t), this.setXYZ(e, Y.x, Y.y, Y.z);
    return this;
  }
  applyMatrix4(t) {
    for (let e = 0, s = this.count; e < s; e++)
      Y.fromBufferAttribute(this, e), Y.applyMatrix4(t), this.setXYZ(e, Y.x, Y.y, Y.z);
    return this;
  }
  applyNormalMatrix(t) {
    for (let e = 0, s = this.count; e < s; e++)
      Y.fromBufferAttribute(this, e), Y.applyNormalMatrix(t), this.setXYZ(e, Y.x, Y.y, Y.z);
    return this;
  }
  transformDirection(t) {
    for (let e = 0, s = this.count; e < s; e++)
      Y.fromBufferAttribute(this, e), Y.transformDirection(t), this.setXYZ(e, Y.x, Y.y, Y.z);
    return this;
  }
  set(t, e = 0) {
    return this.array.set(t, e), this;
  }
  getComponent(t, e) {
    let s = this.array[t * this.itemSize + e];
    return this.normalized && (s = Le(s, this.array)), s;
  }
  setComponent(t, e, s) {
    return this.normalized && (s = it(s, this.array)), this.array[t * this.itemSize + e] = s, this;
  }
  getX(t) {
    let e = this.array[t * this.itemSize];
    return this.normalized && (e = Le(e, this.array)), e;
  }
  setX(t, e) {
    return this.normalized && (e = it(e, this.array)), this.array[t * this.itemSize] = e, this;
  }
  getY(t) {
    let e = this.array[t * this.itemSize + 1];
    return this.normalized && (e = Le(e, this.array)), e;
  }
  setY(t, e) {
    return this.normalized && (e = it(e, this.array)), this.array[t * this.itemSize + 1] = e, this;
  }
  getZ(t) {
    let e = this.array[t * this.itemSize + 2];
    return this.normalized && (e = Le(e, this.array)), e;
  }
  setZ(t, e) {
    return this.normalized && (e = it(e, this.array)), this.array[t * this.itemSize + 2] = e, this;
  }
  getW(t) {
    let e = this.array[t * this.itemSize + 3];
    return this.normalized && (e = Le(e, this.array)), e;
  }
  setW(t, e) {
    return this.normalized && (e = it(e, this.array)), this.array[t * this.itemSize + 3] = e, this;
  }
  setXY(t, e, s) {
    return t *= this.itemSize, this.normalized && (e = it(e, this.array), s = it(s, this.array)), this.array[t + 0] = e, this.array[t + 1] = s, this;
  }
  setXYZ(t, e, s, i) {
    return t *= this.itemSize, this.normalized && (e = it(e, this.array), s = it(s, this.array), i = it(i, this.array)), this.array[t + 0] = e, this.array[t + 1] = s, this.array[t + 2] = i, this;
  }
  setXYZW(t, e, s, i, n) {
    return t *= this.itemSize, this.normalized && (e = it(e, this.array), s = it(s, this.array), i = it(i, this.array), n = it(n, this.array)), this.array[t + 0] = e, this.array[t + 1] = s, this.array[t + 2] = i, this.array[t + 3] = n, this;
  }
  onUpload(t) {
    return this.onUploadCallback = t, this;
  }
  clone() {
    return new this.constructor(this.array, this.itemSize).copy(this);
  }
  toJSON() {
    const t = {
      itemSize: this.itemSize,
      type: this.array.constructor.name,
      array: Array.from(this.array),
      normalized: this.normalized
    };
    return this.name !== "" && (t.name = this.name), this.usage !== 35044 && (t.usage = this.usage), t;
  }
}
class Oh extends q {
  constructor(t, e, s) {
    super(new Uint16Array(t), e, s);
  }
}
class qh extends q {
  constructor(t, e, s) {
    super(new Uint32Array(t), e, s);
  }
}
class ht extends q {
  constructor(t, e, s) {
    super(new Float32Array(t), e, s);
  }
}
let $h = 0;
const ut = /* @__PURE__ */ new nt(), ui = /* @__PURE__ */ new Rt(), xe = /* @__PURE__ */ new R(), ct = /* @__PURE__ */ new St(), ke = /* @__PURE__ */ new St(), J = /* @__PURE__ */ new R();
class rt extends Fs {
  constructor() {
    super(), this.isBufferGeometry = !0, Object.defineProperty(this, "id", { value: $h++ }), this.uuid = Ve(), this.name = "", this.type = "BufferGeometry", this.index = null, this.indirect = null, this.attributes = {}, this.morphAttributes = {}, this.morphTargetsRelative = !1, this.groups = [], this.boundingBox = null, this.boundingSphere = null, this.drawRange = { start: 0, count: 1 / 0 }, this.userData = {};
  }
  getIndex() {
    return this.index;
  }
  setIndex(t) {
    return Array.isArray(t) ? this.index = new (Eh(t) ? qh : Oh)(t, 1) : this.index = t, this;
  }
  setIndirect(t) {
    return this.indirect = t, this;
  }
  getIndirect() {
    return this.indirect;
  }
  getAttribute(t) {
    return this.attributes[t];
  }
  setAttribute(t, e) {
    return this.attributes[t] = e, this;
  }
  deleteAttribute(t) {
    return delete this.attributes[t], this;
  }
  hasAttribute(t) {
    return this.attributes[t] !== void 0;
  }
  addGroup(t, e, s = 0) {
    this.groups.push({
      start: t,
      count: e,
      materialIndex: s
    });
  }
  clearGroups() {
    this.groups = [];
  }
  setDrawRange(t, e) {
    this.drawRange.start = t, this.drawRange.count = e;
  }
  applyMatrix4(t) {
    const e = this.attributes.position;
    e !== void 0 && (e.applyMatrix4(t), e.needsUpdate = !0);
    const s = this.attributes.normal;
    if (s !== void 0) {
      const n = new Ct().getNormalMatrix(t);
      s.applyNormalMatrix(n), s.needsUpdate = !0;
    }
    const i = this.attributes.tangent;
    return i !== void 0 && (i.transformDirection(t), i.needsUpdate = !0), this.boundingBox !== null && this.computeBoundingBox(), this.boundingSphere !== null && this.computeBoundingSphere(), this;
  }
  applyQuaternion(t) {
    return ut.makeRotationFromQuaternion(t), this.applyMatrix4(ut), this;
  }
  rotateX(t) {
    return ut.makeRotationX(t), this.applyMatrix4(ut), this;
  }
  rotateY(t) {
    return ut.makeRotationY(t), this.applyMatrix4(ut), this;
  }
  rotateZ(t) {
    return ut.makeRotationZ(t), this.applyMatrix4(ut), this;
  }
  translate(t, e, s) {
    return ut.makeTranslation(t, e, s), this.applyMatrix4(ut), this;
  }
  scale(t, e, s) {
    return ut.makeScale(t, e, s), this.applyMatrix4(ut), this;
  }
  lookAt(t) {
    return ui.lookAt(t), ui.updateMatrix(), this.applyMatrix4(ui.matrix), this;
  }
  center() {
    return this.computeBoundingBox(), this.boundingBox.getCenter(xe).negate(), this.translate(xe.x, xe.y, xe.z), this;
  }
  setFromPoints(t) {
    const e = this.getAttribute("position");
    if (e === void 0) {
      const s = [];
      for (let i = 0, n = t.length; i < n; i++) {
        const r = t[i];
        s.push(r.x, r.y, r.z || 0);
      }
      this.setAttribute("position", new ht(s, 3));
    } else {
      const s = Math.min(t.length, e.count);
      for (let i = 0; i < s; i++) {
        const n = t[i];
        e.setXYZ(i, n.x, n.y, n.z || 0);
      }
      t.length > e.count && console.warn("THREE.BufferGeometry: Buffer size too small for points data. Use .dispose() and create a new geometry."), e.needsUpdate = !0;
    }
    return this;
  }
  computeBoundingBox() {
    this.boundingBox === null && (this.boundingBox = new St());
    const t = this.attributes.position, e = this.morphAttributes.position;
    if (t && t.isGLBufferAttribute) {
      console.error("THREE.BufferGeometry.computeBoundingBox(): GLBufferAttribute requires a manual bounding box.", this), this.boundingBox.set(
        new R(-1 / 0, -1 / 0, -1 / 0),
        new R(1 / 0, 1 / 0, 1 / 0)
      );
      return;
    }
    if (t !== void 0) {
      if (this.boundingBox.setFromBufferAttribute(t), e)
        for (let s = 0, i = e.length; s < i; s++) {
          const n = e[s];
          ct.setFromBufferAttribute(n), this.morphTargetsRelative ? (J.addVectors(this.boundingBox.min, ct.min), this.boundingBox.expandByPoint(J), J.addVectors(this.boundingBox.max, ct.max), this.boundingBox.expandByPoint(J)) : (this.boundingBox.expandByPoint(ct.min), this.boundingBox.expandByPoint(ct.max));
        }
    } else
      this.boundingBox.makeEmpty();
    (isNaN(this.boundingBox.min.x) || isNaN(this.boundingBox.min.y) || isNaN(this.boundingBox.min.z)) && console.error('THREE.BufferGeometry.computeBoundingBox(): Computed min/max have NaN values. The "position" attribute is likely to have NaN values.', this);
  }
  computeBoundingSphere() {
    this.boundingSphere === null && (this.boundingSphere = new Er());
    const t = this.attributes.position, e = this.morphAttributes.position;
    if (t && t.isGLBufferAttribute) {
      console.error("THREE.BufferGeometry.computeBoundingSphere(): GLBufferAttribute requires a manual bounding sphere.", this), this.boundingSphere.set(new R(), 1 / 0);
      return;
    }
    if (t) {
      const s = this.boundingSphere.center;
      if (ct.setFromBufferAttribute(t), e)
        for (let n = 0, r = e.length; n < r; n++) {
          const o = e[n];
          ke.setFromBufferAttribute(o), this.morphTargetsRelative ? (J.addVectors(ct.min, ke.min), ct.expandByPoint(J), J.addVectors(ct.max, ke.max), ct.expandByPoint(J)) : (ct.expandByPoint(ke.min), ct.expandByPoint(ke.max));
        }
      ct.getCenter(s);
      let i = 0;
      for (let n = 0, r = t.count; n < r; n++)
        J.fromBufferAttribute(t, n), i = Math.max(i, s.distanceToSquared(J));
      if (e)
        for (let n = 0, r = e.length; n < r; n++) {
          const o = e[n], l = this.morphTargetsRelative;
          for (let c = 0, h = o.count; c < h; c++)
            J.fromBufferAttribute(o, c), l && (xe.fromBufferAttribute(t, c), J.add(xe)), i = Math.max(i, s.distanceToSquared(J));
        }
      this.boundingSphere.radius = Math.sqrt(i), isNaN(this.boundingSphere.radius) && console.error('THREE.BufferGeometry.computeBoundingSphere(): Computed radius is NaN. The "position" attribute is likely to have NaN values.', this);
    }
  }
  computeTangents() {
    const t = this.index, e = this.attributes;
    if (t === null || e.position === void 0 || e.normal === void 0 || e.uv === void 0) {
      console.error("THREE.BufferGeometry: .computeTangents() failed. Missing required attributes (index, position, normal or uv)");
      return;
    }
    const s = e.position, i = e.normal, n = e.uv;
    this.hasAttribute("tangent") === !1 && this.setAttribute("tangent", new q(new Float32Array(4 * s.count), 4));
    const r = this.getAttribute("tangent"), o = [], l = [];
    for (let M = 0; M < s.count; M++)
      o[M] = new R(), l[M] = new R();
    const c = new R(), h = new R(), u = new R(), d = new v(), p = new v(), m = new v(), f = new R(), b = new R();
    function y(M, L, k) {
      c.fromBufferAttribute(s, M), h.fromBufferAttribute(s, L), u.fromBufferAttribute(s, k), d.fromBufferAttribute(n, M), p.fromBufferAttribute(n, L), m.fromBufferAttribute(n, k), h.sub(c), u.sub(c), p.sub(d), m.sub(d);
      const w = 1 / (p.x * m.y - m.x * p.y);
      isFinite(w) && (f.copy(h).multiplyScalar(m.y).addScaledVector(u, -p.y).multiplyScalar(w), b.copy(u).multiplyScalar(p.x).addScaledVector(h, -m.x).multiplyScalar(w), o[M].add(f), o[L].add(f), o[k].add(f), l[M].add(b), l[L].add(b), l[k].add(b));
    }
    let x = this.groups;
    x.length === 0 && (x = [{
      start: 0,
      count: t.count
    }]);
    for (let M = 0, L = x.length; M < L; ++M) {
      const k = x[M], w = k.start, E = k.count;
      for (let F = w, X = w + E; F < X; F += 3)
        y(
          t.getX(F + 0),
          t.getX(F + 1),
          t.getX(F + 2)
        );
    }
    const g = new R(), S = new R(), Z = new R(), V = new R();
    function G(M) {
      Z.fromBufferAttribute(i, M), V.copy(Z);
      const L = o[M];
      g.copy(L), g.sub(Z.multiplyScalar(Z.dot(L))).normalize(), S.crossVectors(V, L);
      const w = S.dot(l[M]) < 0 ? -1 : 1;
      r.setXYZW(M, g.x, g.y, g.z, w);
    }
    for (let M = 0, L = x.length; M < L; ++M) {
      const k = x[M], w = k.start, E = k.count;
      for (let F = w, X = w + E; F < X; F += 3)
        G(t.getX(F + 0)), G(t.getX(F + 1)), G(t.getX(F + 2));
    }
  }
  computeVertexNormals() {
    const t = this.index, e = this.getAttribute("position");
    if (e !== void 0) {
      let s = this.getAttribute("normal");
      if (s === void 0)
        s = new q(new Float32Array(e.count * 3), 3), this.setAttribute("normal", s);
      else
        for (let d = 0, p = s.count; d < p; d++)
          s.setXYZ(d, 0, 0, 0);
      const i = new R(), n = new R(), r = new R(), o = new R(), l = new R(), c = new R(), h = new R(), u = new R();
      if (t)
        for (let d = 0, p = t.count; d < p; d += 3) {
          const m = t.getX(d + 0), f = t.getX(d + 1), b = t.getX(d + 2);
          i.fromBufferAttribute(e, m), n.fromBufferAttribute(e, f), r.fromBufferAttribute(e, b), h.subVectors(r, n), u.subVectors(i, n), h.cross(u), o.fromBufferAttribute(s, m), l.fromBufferAttribute(s, f), c.fromBufferAttribute(s, b), o.add(h), l.add(h), c.add(h), s.setXYZ(m, o.x, o.y, o.z), s.setXYZ(f, l.x, l.y, l.z), s.setXYZ(b, c.x, c.y, c.z);
        }
      else
        for (let d = 0, p = e.count; d < p; d += 3)
          i.fromBufferAttribute(e, d + 0), n.fromBufferAttribute(e, d + 1), r.fromBufferAttribute(e, d + 2), h.subVectors(r, n), u.subVectors(i, n), h.cross(u), s.setXYZ(d + 0, h.x, h.y, h.z), s.setXYZ(d + 1, h.x, h.y, h.z), s.setXYZ(d + 2, h.x, h.y, h.z);
      this.normalizeNormals(), s.needsUpdate = !0;
    }
  }
  normalizeNormals() {
    const t = this.attributes.normal;
    for (let e = 0, s = t.count; e < s; e++)
      J.fromBufferAttribute(t, e), J.normalize(), t.setXYZ(e, J.x, J.y, J.z);
  }
  toNonIndexed() {
    function t(o, l) {
      const c = o.array, h = o.itemSize, u = o.normalized, d = new c.constructor(l.length * h);
      let p = 0, m = 0;
      for (let f = 0, b = l.length; f < b; f++) {
        o.isInterleavedBufferAttribute ? p = l[f] * o.data.stride + o.offset : p = l[f] * h;
        for (let y = 0; y < h; y++)
          d[m++] = c[p++];
      }
      return new q(d, h, u);
    }
    if (this.index === null)
      return console.warn("THREE.BufferGeometry.toNonIndexed(): BufferGeometry is already non-indexed."), this;
    const e = new rt(), s = this.index.array, i = this.attributes;
    for (const o in i) {
      const l = i[o], c = t(l, s);
      e.setAttribute(o, c);
    }
    const n = this.morphAttributes;
    for (const o in n) {
      const l = [], c = n[o];
      for (let h = 0, u = c.length; h < u; h++) {
        const d = c[h], p = t(d, s);
        l.push(p);
      }
      e.morphAttributes[o] = l;
    }
    e.morphTargetsRelative = this.morphTargetsRelative;
    const r = this.groups;
    for (let o = 0, l = r.length; o < l; o++) {
      const c = r[o];
      e.addGroup(c.start, c.count, c.materialIndex);
    }
    return e;
  }
  toJSON() {
    const t = {
      metadata: {
        version: 4.6,
        type: "BufferGeometry",
        generator: "BufferGeometry.toJSON"
      }
    };
    if (t.uuid = this.uuid, t.type = this.type, this.name !== "" && (t.name = this.name), Object.keys(this.userData).length > 0 && (t.userData = this.userData), this.parameters !== void 0) {
      const l = this.parameters;
      for (const c in l)
        l[c] !== void 0 && (t[c] = l[c]);
      return t;
    }
    t.data = { attributes: {} };
    const e = this.index;
    e !== null && (t.data.index = {
      type: e.array.constructor.name,
      array: Array.prototype.slice.call(e.array)
    });
    const s = this.attributes;
    for (const l in s) {
      const c = s[l];
      t.data.attributes[l] = c.toJSON(t.data);
    }
    const i = {};
    let n = !1;
    for (const l in this.morphAttributes) {
      const c = this.morphAttributes[l], h = [];
      for (let u = 0, d = c.length; u < d; u++) {
        const p = c[u];
        h.push(p.toJSON(t.data));
      }
      h.length > 0 && (i[l] = h, n = !0);
    }
    n && (t.data.morphAttributes = i, t.data.morphTargetsRelative = this.morphTargetsRelative);
    const r = this.groups;
    r.length > 0 && (t.data.groups = JSON.parse(JSON.stringify(r)));
    const o = this.boundingSphere;
    return o !== null && (t.data.boundingSphere = {
      center: o.center.toArray(),
      radius: o.radius
    }), t;
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    this.index = null, this.attributes = {}, this.morphAttributes = {}, this.groups = [], this.boundingBox = null, this.boundingSphere = null;
    const e = {};
    this.name = t.name;
    const s = t.index;
    s !== null && this.setIndex(s.clone(e));
    const i = t.attributes;
    for (const c in i) {
      const h = i[c];
      this.setAttribute(c, h.clone(e));
    }
    const n = t.morphAttributes;
    for (const c in n) {
      const h = [], u = n[c];
      for (let d = 0, p = u.length; d < p; d++)
        h.push(u[d].clone(e));
      this.morphAttributes[c] = h;
    }
    this.morphTargetsRelative = t.morphTargetsRelative;
    const r = t.groups;
    for (let c = 0, h = r.length; c < h; c++) {
      const u = r[c];
      this.addGroup(u.start, u.count, u.materialIndex);
    }
    const o = t.boundingBox;
    o !== null && (this.boundingBox = o.clone());
    const l = t.boundingSphere;
    return l !== null && (this.boundingSphere = l.clone()), this.drawRange.start = t.drawRange.start, this.drawRange.count = t.drawRange.count, this.userData = t.userData, this;
  }
  dispose() {
    this.dispatchEvent({ type: "dispose" });
  }
}
const Xa = /* @__PURE__ */ new nt(), qt = /* @__PURE__ */ new Br(), hs = /* @__PURE__ */ new Er(), za = /* @__PURE__ */ new R(), us = /* @__PURE__ */ new R(), ds = /* @__PURE__ */ new R(), ps = /* @__PURE__ */ new R(), di = /* @__PURE__ */ new R(), ms = /* @__PURE__ */ new R(), Wa = /* @__PURE__ */ new R(), fs = /* @__PURE__ */ new R();
class gt extends Rt {
  constructor(t = new rt(), e = new ks()) {
    super(), this.isMesh = !0, this.type = "Mesh", this.geometry = t, this.material = e, this.updateMorphTargets();
  }
  copy(t, e) {
    return super.copy(t, e), t.morphTargetInfluences !== void 0 && (this.morphTargetInfluences = t.morphTargetInfluences.slice()), t.morphTargetDictionary !== void 0 && (this.morphTargetDictionary = Object.assign({}, t.morphTargetDictionary)), this.material = Array.isArray(t.material) ? t.material.slice() : t.material, this.geometry = t.geometry, this;
  }
  updateMorphTargets() {
    const e = this.geometry.morphAttributes, s = Object.keys(e);
    if (s.length > 0) {
      const i = e[s[0]];
      if (i !== void 0) {
        this.morphTargetInfluences = [], this.morphTargetDictionary = {};
        for (let n = 0, r = i.length; n < r; n++) {
          const o = i[n].name || String(n);
          this.morphTargetInfluences.push(0), this.morphTargetDictionary[o] = n;
        }
      }
    }
  }
  getVertexPosition(t, e) {
    const s = this.geometry, i = s.attributes.position, n = s.morphAttributes.position, r = s.morphTargetsRelative;
    e.fromBufferAttribute(i, t);
    const o = this.morphTargetInfluences;
    if (n && o) {
      ms.set(0, 0, 0);
      for (let l = 0, c = n.length; l < c; l++) {
        const h = o[l], u = n[l];
        h !== 0 && (di.fromBufferAttribute(u, t), r ? ms.addScaledVector(di, h) : ms.addScaledVector(di.sub(e), h));
      }
      e.add(ms);
    }
    return e;
  }
  raycast(t, e) {
    const s = this.geometry, i = this.material, n = this.matrixWorld;
    i !== void 0 && (s.boundingSphere === null && s.computeBoundingSphere(), hs.copy(s.boundingSphere), hs.applyMatrix4(n), qt.copy(t.ray).recast(t.near), !(hs.containsPoint(qt.origin) === !1 && (qt.intersectSphere(hs, za) === null || qt.origin.distanceToSquared(za) > (t.far - t.near) ** 2)) && (Xa.copy(n).invert(), qt.copy(t.ray).applyMatrix4(Xa), !(s.boundingBox !== null && qt.intersectsBox(s.boundingBox) === !1) && this._computeIntersections(t, e, qt)));
  }
  _computeIntersections(t, e, s) {
    let i;
    const n = this.geometry, r = this.material, o = n.index, l = n.attributes.position, c = n.attributes.uv, h = n.attributes.uv1, u = n.attributes.normal, d = n.groups, p = n.drawRange;
    if (o !== null)
      if (Array.isArray(r))
        for (let m = 0, f = d.length; m < f; m++) {
          const b = d[m], y = r[b.materialIndex], x = Math.max(b.start, p.start), g = Math.min(o.count, Math.min(b.start + b.count, p.start + p.count));
          for (let S = x, Z = g; S < Z; S += 3) {
            const V = o.getX(S), G = o.getX(S + 1), M = o.getX(S + 2);
            i = bs(this, y, t, s, c, h, u, V, G, M), i && (i.faceIndex = Math.floor(S / 3), i.face.materialIndex = b.materialIndex, e.push(i));
          }
        }
      else {
        const m = Math.max(0, p.start), f = Math.min(o.count, p.start + p.count);
        for (let b = m, y = f; b < y; b += 3) {
          const x = o.getX(b), g = o.getX(b + 1), S = o.getX(b + 2);
          i = bs(this, r, t, s, c, h, u, x, g, S), i && (i.faceIndex = Math.floor(b / 3), e.push(i));
        }
      }
    else if (l !== void 0)
      if (Array.isArray(r))
        for (let m = 0, f = d.length; m < f; m++) {
          const b = d[m], y = r[b.materialIndex], x = Math.max(b.start, p.start), g = Math.min(l.count, Math.min(b.start + b.count, p.start + p.count));
          for (let S = x, Z = g; S < Z; S += 3) {
            const V = S, G = S + 1, M = S + 2;
            i = bs(this, y, t, s, c, h, u, V, G, M), i && (i.faceIndex = Math.floor(S / 3), i.face.materialIndex = b.materialIndex, e.push(i));
          }
        }
      else {
        const m = Math.max(0, p.start), f = Math.min(l.count, p.start + p.count);
        for (let b = m, y = f; b < y; b += 3) {
          const x = b, g = b + 1, S = b + 2;
          i = bs(this, r, t, s, c, h, u, x, g, S), i && (i.faceIndex = Math.floor(b / 3), e.push(i));
        }
      }
  }
}
function tu(a, t, e, s, i, n, r, o) {
  let l;
  if (t.side === 1 ? l = s.intersectTriangle(r, n, i, !0, o) : l = s.intersectTriangle(i, n, r, t.side === 0, o), l === null) return null;
  fs.copy(o), fs.applyMatrix4(a.matrixWorld);
  const c = e.ray.origin.distanceTo(fs);
  return c < e.near || c > e.far ? null : {
    distance: c,
    point: fs.clone(),
    object: a
  };
}
function bs(a, t, e, s, i, n, r, o, l, c) {
  a.getVertexPosition(o, us), a.getVertexPosition(l, ds), a.getVertexPosition(c, ps);
  const h = tu(a, t, e, s, us, ds, ps, Wa);
  if (h) {
    const u = new R();
    xt.getBarycoord(Wa, us, ds, ps, u), i && (h.uv = xt.getInterpolatedAttribute(i, o, l, c, u, new v())), n && (h.uv1 = xt.getInterpolatedAttribute(n, o, l, c, u, new v())), r && (h.normal = xt.getInterpolatedAttribute(r, o, l, c, u, new R()), h.normal.dot(s.direction) > 0 && h.normal.multiplyScalar(-1));
    const d = {
      a: o,
      b: l,
      c,
      normal: new R(),
      materialIndex: 0
    };
    xt.getNormal(us, ds, ps, d.normal), h.face = d, h.barycoord = u;
  }
  return h;
}
class Nr extends rt {
  constructor(t = 1, e = 1, s = 1, i = 1, n = 1, r = 1) {
    super(), this.type = "BoxGeometry", this.parameters = {
      width: t,
      height: e,
      depth: s,
      widthSegments: i,
      heightSegments: n,
      depthSegments: r
    };
    const o = this;
    i = Math.floor(i), n = Math.floor(n), r = Math.floor(r);
    const l = [], c = [], h = [], u = [];
    let d = 0, p = 0;
    m("z", "y", "x", -1, -1, s, e, t, r, n, 0), m("z", "y", "x", 1, -1, s, e, -t, r, n, 1), m("x", "z", "y", 1, 1, t, s, e, i, r, 2), m("x", "z", "y", 1, -1, t, s, -e, i, r, 3), m("x", "y", "z", 1, -1, t, e, s, i, n, 4), m("x", "y", "z", -1, -1, t, e, -s, i, n, 5), this.setIndex(l), this.setAttribute("position", new ht(c, 3)), this.setAttribute("normal", new ht(h, 3)), this.setAttribute("uv", new ht(u, 2));
    function m(f, b, y, x, g, S, Z, V, G, M, L) {
      const k = S / G, w = Z / M, E = S / 2, F = Z / 2, X = V / 2, I = G + 1, H = M + 1;
      let ot = 0, $ = 0;
      const N = new R();
      for (let _ = 0; _ < H; _++) {
        const K = _ * w - F;
        for (let Et = 0; Et < I; Et++) {
          const re = Et * k - E;
          N[f] = re * x, N[b] = K * g, N[y] = X, c.push(N.x, N.y, N.z), N[f] = 0, N[b] = 0, N[y] = V > 0 ? 1 : -1, h.push(N.x, N.y, N.z), u.push(Et / G), u.push(1 - _ / M), ot += 1;
        }
      }
      for (let _ = 0; _ < M; _++)
        for (let K = 0; K < G; K++) {
          const Et = d + K + I * _, re = d + K + I * (_ + 1), Hs = d + (K + 1) + I * (_ + 1), je = d + (K + 1) + I * _;
          l.push(Et, re, je), l.push(re, Hs, je), $ += 6;
        }
      o.addGroup(p, $, L), p += $, d += ot;
    }
  }
  copy(t) {
    return super.copy(t), this.parameters = Object.assign({}, t.parameters), this;
  }
  static fromJSON(t) {
    return new Nr(t.width, t.height, t.depth, t.widthSegments, t.heightSegments, t.depthSegments);
  }
}
const pi = /* @__PURE__ */ new R(), eu = /* @__PURE__ */ new R(), su = /* @__PURE__ */ new Ct();
class iu {
  constructor(t = new R(1, 0, 0), e = 0) {
    this.isPlane = !0, this.normal = t, this.constant = e;
  }
  set(t, e) {
    return this.normal.copy(t), this.constant = e, this;
  }
  setComponents(t, e, s, i) {
    return this.normal.set(t, e, s), this.constant = i, this;
  }
  setFromNormalAndCoplanarPoint(t, e) {
    return this.normal.copy(t), this.constant = -e.dot(this.normal), this;
  }
  setFromCoplanarPoints(t, e, s) {
    const i = pi.subVectors(s, e).cross(eu.subVectors(t, e)).normalize();
    return this.setFromNormalAndCoplanarPoint(i, t), this;
  }
  copy(t) {
    return this.normal.copy(t.normal), this.constant = t.constant, this;
  }
  normalize() {
    const t = 1 / this.normal.length();
    return this.normal.multiplyScalar(t), this.constant *= t, this;
  }
  negate() {
    return this.constant *= -1, this.normal.negate(), this;
  }
  distanceToPoint(t) {
    return this.normal.dot(t) + this.constant;
  }
  distanceToSphere(t) {
    return this.distanceToPoint(t.center) - t.radius;
  }
  projectPoint(t, e) {
    return e.copy(t).addScaledVector(this.normal, -this.distanceToPoint(t));
  }
  intersectLine(t, e) {
    const s = t.delta(pi), i = this.normal.dot(s);
    if (i === 0)
      return this.distanceToPoint(t.start) === 0 ? e.copy(t.start) : null;
    const n = -(t.start.dot(this.normal) + this.constant) / i;
    return n < 0 || n > 1 ? null : e.copy(t.start).addScaledVector(s, n);
  }
  intersectsLine(t) {
    const e = this.distanceToPoint(t.start), s = this.distanceToPoint(t.end);
    return e < 0 && s > 0 || s < 0 && e > 0;
  }
  intersectsBox(t) {
    return t.intersectsPlane(this);
  }
  intersectsSphere(t) {
    return t.intersectsPlane(this);
  }
  coplanarPoint(t) {
    return t.copy(this.normal).multiplyScalar(-this.constant);
  }
  applyMatrix4(t, e) {
    const s = e || su.getNormalMatrix(t), i = this.coplanarPoint(pi).applyMatrix4(t), n = this.normal.applyMatrix3(s).normalize();
    return this.constant = -i.dot(n), this;
  }
  translate(t) {
    return this.constant -= t.dot(this.normal), this;
  }
  equals(t) {
    return t.normal.equals(this.normal) && t.constant === this.constant;
  }
  clone() {
    return new this.constructor().copy(this);
  }
}
class _r extends Ur {
  constructor(t) {
    super(), this.isLineBasicMaterial = !0, this.type = "LineBasicMaterial", this.color = new Jt(16777215), this.map = null, this.linewidth = 1, this.linecap = "round", this.linejoin = "round", this.fog = !0, this.setValues(t);
  }
  copy(t) {
    return super.copy(t), this.color.copy(t.color), this.map = t.map, this.linewidth = t.linewidth, this.linecap = t.linecap, this.linejoin = t.linejoin, this.fog = t.fog, this;
  }
}
const ws = /* @__PURE__ */ new R(), vs = /* @__PURE__ */ new R(), Ia = /* @__PURE__ */ new nt(), Te = /* @__PURE__ */ new Br(), ys = /* @__PURE__ */ new Er(), mi = /* @__PURE__ */ new R(), Ca = /* @__PURE__ */ new R();
class Xt extends Rt {
  constructor(t = new rt(), e = new _r()) {
    super(), this.isLine = !0, this.type = "Line", this.geometry = t, this.material = e, this.updateMorphTargets();
  }
  copy(t, e) {
    return super.copy(t, e), this.material = Array.isArray(t.material) ? t.material.slice() : t.material, this.geometry = t.geometry, this;
  }
  computeLineDistances() {
    const t = this.geometry;
    if (t.index === null) {
      const e = t.attributes.position, s = [0];
      for (let i = 1, n = e.count; i < n; i++)
        ws.fromBufferAttribute(e, i - 1), vs.fromBufferAttribute(e, i), s[i] = s[i - 1], s[i] += ws.distanceTo(vs);
      t.setAttribute("lineDistance", new ht(s, 1));
    } else
      console.warn("THREE.Line.computeLineDistances(): Computation only possible with non-indexed BufferGeometry.");
    return this;
  }
  raycast(t, e) {
    const s = this.geometry, i = this.matrixWorld, n = t.params.Line.threshold, r = s.drawRange;
    if (s.boundingSphere === null && s.computeBoundingSphere(), ys.copy(s.boundingSphere), ys.applyMatrix4(i), ys.radius += n, t.ray.intersectsSphere(ys) === !1) return;
    Ia.copy(i).invert(), Te.copy(t.ray).applyMatrix4(Ia);
    const o = n / ((this.scale.x + this.scale.y + this.scale.z) / 3), l = o * o, c = this.isLineSegments ? 2 : 1, h = s.index, d = s.attributes.position;
    if (h !== null) {
      const p = Math.max(0, r.start), m = Math.min(h.count, r.start + r.count);
      for (let f = p, b = m - 1; f < b; f += c) {
        const y = h.getX(f), x = h.getX(f + 1), g = xs(this, t, Te, l, y, x);
        g && e.push(g);
      }
      if (this.isLineLoop) {
        const f = h.getX(m - 1), b = h.getX(p), y = xs(this, t, Te, l, f, b);
        y && e.push(y);
      }
    } else {
      const p = Math.max(0, r.start), m = Math.min(d.count, r.start + r.count);
      for (let f = p, b = m - 1; f < b; f += c) {
        const y = xs(this, t, Te, l, f, f + 1);
        y && e.push(y);
      }
      if (this.isLineLoop) {
        const f = xs(this, t, Te, l, m - 1, p);
        f && e.push(f);
      }
    }
  }
  updateMorphTargets() {
    const e = this.geometry.morphAttributes, s = Object.keys(e);
    if (s.length > 0) {
      const i = e[s[0]];
      if (i !== void 0) {
        this.morphTargetInfluences = [], this.morphTargetDictionary = {};
        for (let n = 0, r = i.length; n < r; n++) {
          const o = i[n].name || String(n);
          this.morphTargetInfluences.push(0), this.morphTargetDictionary[o] = n;
        }
      }
    }
  }
}
function xs(a, t, e, s, i, n) {
  const r = a.geometry.attributes.position;
  if (ws.fromBufferAttribute(r, i), vs.fromBufferAttribute(r, n), e.distanceSqToSegment(ws, vs, mi, Ca) > s) return;
  mi.applyMatrix4(a.matrixWorld);
  const l = t.ray.origin.distanceTo(mi);
  if (!(l < t.near || l > t.far))
    return {
      distance: l,
      // What do we want? intersection point on the ray or on the segment??
      // point: raycaster.ray.at( distance ),
      point: Ca.clone().applyMatrix4(a.matrixWorld),
      index: i,
      face: null,
      faceIndex: null,
      barycoord: null,
      object: a
    };
}
const Ea = /* @__PURE__ */ new R(), Ba = /* @__PURE__ */ new R();
class Ts extends Xt {
  constructor(t, e) {
    super(t, e), this.isLineSegments = !0, this.type = "LineSegments";
  }
  computeLineDistances() {
    const t = this.geometry;
    if (t.index === null) {
      const e = t.attributes.position, s = [];
      for (let i = 0, n = e.count; i < n; i += 2)
        Ea.fromBufferAttribute(e, i), Ba.fromBufferAttribute(e, i + 1), s[i] = i === 0 ? 0 : s[i - 1], s[i + 1] = s[i] + Ea.distanceTo(Ba);
      t.setAttribute("lineDistance", new ht(s, 1));
    } else
      console.warn("THREE.LineSegments.computeLineDistances(): Computation only possible with non-indexed BufferGeometry.");
    return this;
  }
}
class at extends Rt {
  constructor() {
    super(), this.isGroup = !0, this.type = "Group";
  }
}
class nu extends It {
  constructor(t, e, s, i, n, r, o, l, c) {
    super(t, e, s, i, n, r, o, l, c), this.isCanvasTexture = !0, this.needsUpdate = !0;
  }
}
class Zt {
  constructor() {
    this.type = "Curve", this.arcLengthDivisions = 200;
  }
  // Virtual base class method to overwrite and implement in subclasses
  //	- t [0 .. 1]
  getPoint() {
    return console.warn("THREE.Curve: .getPoint() not implemented."), null;
  }
  // Get point at relative position in curve according to arc length
  // - u [0 .. 1]
  getPointAt(t, e) {
    const s = this.getUtoTmapping(t);
    return this.getPoint(s, e);
  }
  // Get sequence of points using getPoint( t )
  getPoints(t = 5) {
    const e = [];
    for (let s = 0; s <= t; s++)
      e.push(this.getPoint(s / t));
    return e;
  }
  // Get sequence of points using getPointAt( u )
  getSpacedPoints(t = 5) {
    const e = [];
    for (let s = 0; s <= t; s++)
      e.push(this.getPointAt(s / t));
    return e;
  }
  // Get total curve arc length
  getLength() {
    const t = this.getLengths();
    return t[t.length - 1];
  }
  // Get list of cumulative segment lengths
  getLengths(t = this.arcLengthDivisions) {
    if (this.cacheArcLengths && this.cacheArcLengths.length === t + 1 && !this.needsUpdate)
      return this.cacheArcLengths;
    this.needsUpdate = !1;
    const e = [];
    let s, i = this.getPoint(0), n = 0;
    e.push(0);
    for (let r = 1; r <= t; r++)
      s = this.getPoint(r / t), n += s.distanceTo(i), e.push(n), i = s;
    return this.cacheArcLengths = e, e;
  }
  updateArcLengths() {
    this.needsUpdate = !0, this.getLengths();
  }
  // Given u ( 0 .. 1 ), get a t to find p. This gives you points which are equidistant
  getUtoTmapping(t, e) {
    const s = this.getLengths();
    let i = 0;
    const n = s.length;
    let r;
    e ? r = e : r = t * s[n - 1];
    let o = 0, l = n - 1, c;
    for (; o <= l; )
      if (i = Math.floor(o + (l - o) / 2), c = s[i] - r, c < 0)
        o = i + 1;
      else if (c > 0)
        l = i - 1;
      else {
        l = i;
        break;
      }
    if (i = l, s[i] === r)
      return i / (n - 1);
    const h = s[i], d = s[i + 1] - h, p = (r - h) / d;
    return (i + p) / (n - 1);
  }
  // Returns a unit vector tangent at t
  // In case any sub curve does not implement its tangent derivation,
  // 2 points a small delta apart will be used to find its gradient
  // which seems to give a reasonable approximation
  getTangent(t, e) {
    let i = t - 1e-4, n = t + 1e-4;
    i < 0 && (i = 0), n > 1 && (n = 1);
    const r = this.getPoint(i), o = this.getPoint(n), l = e || (r.isVector2 ? new v() : new R());
    return l.copy(o).sub(r).normalize(), l;
  }
  getTangentAt(t, e) {
    const s = this.getUtoTmapping(t);
    return this.getTangent(s, e);
  }
  computeFrenetFrames(t, e) {
    const s = new R(), i = [], n = [], r = [], o = new R(), l = new nt();
    for (let p = 0; p <= t; p++) {
      const m = p / t;
      i[p] = this.getTangentAt(m, new R());
    }
    n[0] = new R(), r[0] = new R();
    let c = Number.MAX_VALUE;
    const h = Math.abs(i[0].x), u = Math.abs(i[0].y), d = Math.abs(i[0].z);
    h <= c && (c = h, s.set(1, 0, 0)), u <= c && (c = u, s.set(0, 1, 0)), d <= c && s.set(0, 0, 1), o.crossVectors(i[0], s).normalize(), n[0].crossVectors(i[0], o), r[0].crossVectors(i[0], n[0]);
    for (let p = 1; p <= t; p++) {
      if (n[p] = n[p - 1].clone(), r[p] = r[p - 1].clone(), o.crossVectors(i[p - 1], i[p]), o.length() > Number.EPSILON) {
        o.normalize();
        const m = Math.acos(T(i[p - 1].dot(i[p]), -1, 1));
        n[p].applyMatrix4(l.makeRotationAxis(o, m));
      }
      r[p].crossVectors(i[p], n[p]);
    }
    if (e === !0) {
      let p = Math.acos(T(n[0].dot(n[t]), -1, 1));
      p /= t, i[0].dot(o.crossVectors(n[0], n[t])) > 0 && (p = -p);
      for (let m = 1; m <= t; m++)
        n[m].applyMatrix4(l.makeRotationAxis(i[m], p * m)), r[m].crossVectors(i[m], n[m]);
    }
    return {
      tangents: i,
      normals: n,
      binormals: r
    };
  }
  clone() {
    return new this.constructor().copy(this);
  }
  copy(t) {
    return this.arcLengthDivisions = t.arcLengthDivisions, this;
  }
  toJSON() {
    const t = {
      metadata: {
        version: 4.6,
        type: "Curve",
        generator: "Curve.toJSON"
      }
    };
    return t.arcLengthDivisions = this.arcLengthDivisions, t.type = this.type, t;
  }
  fromJSON(t) {
    return this.arcLengthDivisions = t.arcLengthDivisions, this;
  }
}
class Ae extends Zt {
  constructor(t = 0, e = 0, s = 1, i = 1, n = 0, r = Math.PI * 2, o = !1, l = 0) {
    super(), this.isEllipseCurve = !0, this.type = "EllipseCurve", this.aX = t, this.aY = e, this.xRadius = s, this.yRadius = i, this.aStartAngle = n, this.aEndAngle = r, this.aClockwise = o, this.aRotation = l;
  }
  getPoint(t, e = new v()) {
    const s = e, i = Math.PI * 2;
    let n = this.aEndAngle - this.aStartAngle;
    const r = Math.abs(n) < Number.EPSILON;
    for (; n < 0; ) n += i;
    for (; n > i; ) n -= i;
    n < Number.EPSILON && (r ? n = 0 : n = i), this.aClockwise === !0 && !r && (n === i ? n = -i : n = n - i);
    const o = this.aStartAngle + t * n;
    let l = this.aX + this.xRadius * Math.cos(o), c = this.aY + this.yRadius * Math.sin(o);
    if (this.aRotation !== 0) {
      const h = Math.cos(this.aRotation), u = Math.sin(this.aRotation), d = l - this.aX, p = c - this.aY;
      l = d * h - p * u + this.aX, c = d * u + p * h + this.aY;
    }
    return s.set(l, c);
  }
  copy(t) {
    return super.copy(t), this.aX = t.aX, this.aY = t.aY, this.xRadius = t.xRadius, this.yRadius = t.yRadius, this.aStartAngle = t.aStartAngle, this.aEndAngle = t.aEndAngle, this.aClockwise = t.aClockwise, this.aRotation = t.aRotation, this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.aX = this.aX, t.aY = this.aY, t.xRadius = this.xRadius, t.yRadius = this.yRadius, t.aStartAngle = this.aStartAngle, t.aEndAngle = this.aEndAngle, t.aClockwise = this.aClockwise, t.aRotation = this.aRotation, t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.aX = t.aX, this.aY = t.aY, this.xRadius = t.xRadius, this.yRadius = t.yRadius, this.aStartAngle = t.aStartAngle, this.aEndAngle = t.aEndAngle, this.aClockwise = t.aClockwise, this.aRotation = t.aRotation, this;
  }
}
class Hr extends Ae {
  constructor(t, e, s, i, n, r) {
    super(t, e, s, s, i, n, r), this.isArcCurve = !0, this.type = "ArcCurve";
  }
}
function Yr() {
  let a = 0, t = 0, e = 0, s = 0;
  function i(n, r, o, l) {
    a = n, t = o, e = -3 * n + 3 * r - 2 * o - l, s = 2 * n - 2 * r + o + l;
  }
  return {
    initCatmullRom: function(n, r, o, l, c) {
      i(r, o, c * (o - n), c * (l - r));
    },
    initNonuniformCatmullRom: function(n, r, o, l, c, h, u) {
      let d = (r - n) / c - (o - n) / (c + h) + (o - r) / h, p = (o - r) / h - (l - r) / (h + u) + (l - o) / u;
      d *= h, p *= h, i(r, o, d, p);
    },
    calc: function(n) {
      const r = n * n, o = r * n;
      return a + t * n + e * r + s * o;
    }
  };
}
const gs = /* @__PURE__ */ new R(), fi = /* @__PURE__ */ new Yr(), bi = /* @__PURE__ */ new Yr(), yi = /* @__PURE__ */ new Yr();
class ru extends Zt {
  constructor(t = [], e = !1, s = "centripetal", i = 0.5) {
    super(), this.isCatmullRomCurve3 = !0, this.type = "CatmullRomCurve3", this.points = t, this.closed = e, this.curveType = s, this.tension = i;
  }
  getPoint(t, e = new R()) {
    const s = e, i = this.points, n = i.length, r = (n - (this.closed ? 0 : 1)) * t;
    let o = Math.floor(r), l = r - o;
    this.closed ? o += o > 0 ? 0 : (Math.floor(Math.abs(o) / n) + 1) * n : l === 0 && o === n - 1 && (o = n - 2, l = 1);
    let c, h;
    this.closed || o > 0 ? c = i[(o - 1) % n] : (gs.subVectors(i[0], i[1]).add(i[0]), c = gs);
    const u = i[o % n], d = i[(o + 1) % n];
    if (this.closed || o + 2 < n ? h = i[(o + 2) % n] : (gs.subVectors(i[n - 1], i[n - 2]).add(i[n - 1]), h = gs), this.curveType === "centripetal" || this.curveType === "chordal") {
      const p = this.curveType === "chordal" ? 0.5 : 0.25;
      let m = Math.pow(c.distanceToSquared(u), p), f = Math.pow(u.distanceToSquared(d), p), b = Math.pow(d.distanceToSquared(h), p);
      f < 1e-4 && (f = 1), m < 1e-4 && (m = f), b < 1e-4 && (b = f), fi.initNonuniformCatmullRom(c.x, u.x, d.x, h.x, m, f, b), bi.initNonuniformCatmullRom(c.y, u.y, d.y, h.y, m, f, b), yi.initNonuniformCatmullRom(c.z, u.z, d.z, h.z, m, f, b);
    } else this.curveType === "catmullrom" && (fi.initCatmullRom(c.x, u.x, d.x, h.x, this.tension), bi.initCatmullRom(c.y, u.y, d.y, h.y, this.tension), yi.initCatmullRom(c.z, u.z, d.z, h.z, this.tension));
    return s.set(
      fi.calc(l),
      bi.calc(l),
      yi.calc(l)
    ), s;
  }
  copy(t) {
    super.copy(t), this.points = [];
    for (let e = 0, s = t.points.length; e < s; e++) {
      const i = t.points[e];
      this.points.push(i.clone());
    }
    return this.closed = t.closed, this.curveType = t.curveType, this.tension = t.tension, this;
  }
  toJSON() {
    const t = super.toJSON();
    t.points = [];
    for (let e = 0, s = this.points.length; e < s; e++) {
      const i = this.points[e];
      t.points.push(i.toArray());
    }
    return t.closed = this.closed, t.curveType = this.curveType, t.tension = this.tension, t;
  }
  fromJSON(t) {
    super.fromJSON(t), this.points = [];
    for (let e = 0, s = t.points.length; e < s; e++) {
      const i = t.points[e];
      this.points.push(new R().fromArray(i));
    }
    return this.closed = t.closed, this.curveType = t.curveType, this.tension = t.tension, this;
  }
}
function Ua(a, t, e, s, i) {
  const n = (s - t) * 0.5, r = (i - e) * 0.5, o = a * a, l = a * o;
  return (2 * e - 2 * s + n + r) * l + (-3 * e + 3 * s - 2 * n - r) * o + n * a + e;
}
function au(a, t) {
  const e = 1 - a;
  return e * e * t;
}
function ou(a, t) {
  return 2 * (1 - a) * a * t;
}
function lu(a, t) {
  return a * a * t;
}
function Ee(a, t, e, s) {
  return au(a, t) + ou(a, e) + lu(a, s);
}
function cu(a, t) {
  const e = 1 - a;
  return e * e * e * t;
}
function hu(a, t) {
  const e = 1 - a;
  return 3 * e * e * a * t;
}
function uu(a, t) {
  return 3 * (1 - a) * a * a * t;
}
function du(a, t) {
  return a * a * a * t;
}
function Be(a, t, e, s, i) {
  return cu(a, t) + hu(a, e) + uu(a, s) + du(a, i);
}
class Zc extends Zt {
  constructor(t = new v(), e = new v(), s = new v(), i = new v()) {
    super(), this.isCubicBezierCurve = !0, this.type = "CubicBezierCurve", this.v0 = t, this.v1 = e, this.v2 = s, this.v3 = i;
  }
  getPoint(t, e = new v()) {
    const s = e, i = this.v0, n = this.v1, r = this.v2, o = this.v3;
    return s.set(
      Be(t, i.x, n.x, r.x, o.x),
      Be(t, i.y, n.y, r.y, o.y)
    ), s;
  }
  copy(t) {
    return super.copy(t), this.v0.copy(t.v0), this.v1.copy(t.v1), this.v2.copy(t.v2), this.v3.copy(t.v3), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v0 = this.v0.toArray(), t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t.v3 = this.v3.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v0.fromArray(t.v0), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this.v3.fromArray(t.v3), this;
  }
}
class pu extends Zt {
  constructor(t = new R(), e = new R(), s = new R(), i = new R()) {
    super(), this.isCubicBezierCurve3 = !0, this.type = "CubicBezierCurve3", this.v0 = t, this.v1 = e, this.v2 = s, this.v3 = i;
  }
  getPoint(t, e = new R()) {
    const s = e, i = this.v0, n = this.v1, r = this.v2, o = this.v3;
    return s.set(
      Be(t, i.x, n.x, r.x, o.x),
      Be(t, i.y, n.y, r.y, o.y),
      Be(t, i.z, n.z, r.z, o.z)
    ), s;
  }
  copy(t) {
    return super.copy(t), this.v0.copy(t.v0), this.v1.copy(t.v1), this.v2.copy(t.v2), this.v3.copy(t.v3), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v0 = this.v0.toArray(), t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t.v3 = this.v3.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v0.fromArray(t.v0), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this.v3.fromArray(t.v3), this;
  }
}
class Gc extends Zt {
  constructor(t = new v(), e = new v()) {
    super(), this.isLineCurve = !0, this.type = "LineCurve", this.v1 = t, this.v2 = e;
  }
  getPoint(t, e = new v()) {
    const s = e;
    return t === 1 ? s.copy(this.v2) : (s.copy(this.v2).sub(this.v1), s.multiplyScalar(t).add(this.v1)), s;
  }
  // Line curve is linear, so we can overwrite default getPointAt
  getPointAt(t, e) {
    return this.getPoint(t, e);
  }
  getTangent(t, e = new v()) {
    return e.subVectors(this.v2, this.v1).normalize();
  }
  getTangentAt(t, e) {
    return this.getTangent(t, e);
  }
  copy(t) {
    return super.copy(t), this.v1.copy(t.v1), this.v2.copy(t.v2), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this;
  }
}
class mu extends Zt {
  constructor(t = new R(), e = new R()) {
    super(), this.isLineCurve3 = !0, this.type = "LineCurve3", this.v1 = t, this.v2 = e;
  }
  getPoint(t, e = new R()) {
    const s = e;
    return t === 1 ? s.copy(this.v2) : (s.copy(this.v2).sub(this.v1), s.multiplyScalar(t).add(this.v1)), s;
  }
  // Line curve is linear, so we can overwrite default getPointAt
  getPointAt(t, e) {
    return this.getPoint(t, e);
  }
  getTangent(t, e = new R()) {
    return e.subVectors(this.v2, this.v1).normalize();
  }
  getTangentAt(t, e) {
    return this.getTangent(t, e);
  }
  copy(t) {
    return super.copy(t), this.v1.copy(t.v1), this.v2.copy(t.v2), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this;
  }
}
class Vc extends Zt {
  constructor(t = new v(), e = new v(), s = new v()) {
    super(), this.isQuadraticBezierCurve = !0, this.type = "QuadraticBezierCurve", this.v0 = t, this.v1 = e, this.v2 = s;
  }
  getPoint(t, e = new v()) {
    const s = e, i = this.v0, n = this.v1, r = this.v2;
    return s.set(
      Ee(t, i.x, n.x, r.x),
      Ee(t, i.y, n.y, r.y)
    ), s;
  }
  copy(t) {
    return super.copy(t), this.v0.copy(t.v0), this.v1.copy(t.v1), this.v2.copy(t.v2), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v0 = this.v0.toArray(), t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v0.fromArray(t.v0), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this;
  }
}
class fu extends Zt {
  constructor(t = new R(), e = new R(), s = new R()) {
    super(), this.isQuadraticBezierCurve3 = !0, this.type = "QuadraticBezierCurve3", this.v0 = t, this.v1 = e, this.v2 = s;
  }
  getPoint(t, e = new R()) {
    const s = e, i = this.v0, n = this.v1, r = this.v2;
    return s.set(
      Ee(t, i.x, n.x, r.x),
      Ee(t, i.y, n.y, r.y),
      Ee(t, i.z, n.z, r.z)
    ), s;
  }
  copy(t) {
    return super.copy(t), this.v0.copy(t.v0), this.v1.copy(t.v1), this.v2.copy(t.v2), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.v0 = this.v0.toArray(), t.v1 = this.v1.toArray(), t.v2 = this.v2.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.v0.fromArray(t.v0), this.v1.fromArray(t.v1), this.v2.fromArray(t.v2), this;
  }
}
class Mc extends Zt {
  constructor(t = []) {
    super(), this.isSplineCurve = !0, this.type = "SplineCurve", this.points = t;
  }
  getPoint(t, e = new v()) {
    const s = e, i = this.points, n = (i.length - 1) * t, r = Math.floor(n), o = n - r, l = i[r === 0 ? r : r - 1], c = i[r], h = i[r > i.length - 2 ? i.length - 1 : r + 1], u = i[r > i.length - 3 ? i.length - 1 : r + 2];
    return s.set(
      Ua(o, l.x, c.x, h.x, u.x),
      Ua(o, l.y, c.y, h.y, u.y)
    ), s;
  }
  copy(t) {
    super.copy(t), this.points = [];
    for (let e = 0, s = t.points.length; e < s; e++) {
      const i = t.points[e];
      this.points.push(i.clone());
    }
    return this;
  }
  toJSON() {
    const t = super.toJSON();
    t.points = [];
    for (let e = 0, s = this.points.length; e < s; e++) {
      const i = this.points[e];
      t.points.push(i.toArray());
    }
    return t;
  }
  fromJSON(t) {
    super.fromJSON(t), this.points = [];
    for (let e = 0, s = t.points.length; e < s; e++) {
      const i = t.points[e];
      this.points.push(new v().fromArray(i));
    }
    return this;
  }
}
var Na = /* @__PURE__ */ Object.freeze({
  __proto__: null,
  ArcCurve: Hr,
  CatmullRomCurve3: ru,
  CubicBezierCurve: Zc,
  CubicBezierCurve3: pu,
  EllipseCurve: Ae,
  LineCurve: Gc,
  LineCurve3: mu,
  QuadraticBezierCurve: Vc,
  QuadraticBezierCurve3: fu,
  SplineCurve: Mc
});
class bu extends Zt {
  constructor() {
    super(), this.type = "CurvePath", this.curves = [], this.autoClose = !1;
  }
  add(t) {
    this.curves.push(t);
  }
  closePath() {
    const t = this.curves[0].getPoint(0), e = this.curves[this.curves.length - 1].getPoint(1);
    if (!t.equals(e)) {
      const s = t.isVector2 === !0 ? "LineCurve" : "LineCurve3";
      this.curves.push(new Na[s](e, t));
    }
    return this;
  }
  // To get accurate point with reference to
  // entire path distance at time t,
  // following has to be done:
  // 1. Length of each sub path have to be known
  // 2. Locate and identify type of curve
  // 3. Get t for the curve
  // 4. Return curve.getPointAt(t')
  getPoint(t, e) {
    const s = t * this.getLength(), i = this.getCurveLengths();
    let n = 0;
    for (; n < i.length; ) {
      if (i[n] >= s) {
        const r = i[n] - s, o = this.curves[n], l = o.getLength(), c = l === 0 ? 0 : 1 - r / l;
        return o.getPointAt(c, e);
      }
      n++;
    }
    return null;
  }
  // We cannot use the default THREE.Curve getPoint() with getLength() because in
  // THREE.Curve, getLength() depends on getPoint() but in THREE.CurvePath
  // getPoint() depends on getLength
  getLength() {
    const t = this.getCurveLengths();
    return t[t.length - 1];
  }
  // cacheLengths must be recalculated.
  updateArcLengths() {
    this.needsUpdate = !0, this.cacheLengths = null, this.getCurveLengths();
  }
  // Compute lengths and cache them
  // We cannot overwrite getLengths() because UtoT mapping uses it.
  getCurveLengths() {
    if (this.cacheLengths && this.cacheLengths.length === this.curves.length)
      return this.cacheLengths;
    const t = [];
    let e = 0;
    for (let s = 0, i = this.curves.length; s < i; s++)
      e += this.curves[s].getLength(), t.push(e);
    return this.cacheLengths = t, t;
  }
  getSpacedPoints(t = 40) {
    const e = [];
    for (let s = 0; s <= t; s++)
      e.push(this.getPoint(s / t));
    return this.autoClose && e.push(e[0]), e;
  }
  getPoints(t = 12) {
    const e = [];
    let s;
    for (let i = 0, n = this.curves; i < n.length; i++) {
      const r = n[i], o = r.isEllipseCurve ? t * 2 : r.isLineCurve || r.isLineCurve3 ? 1 : r.isSplineCurve ? t * r.points.length : t, l = r.getPoints(o);
      for (let c = 0; c < l.length; c++) {
        const h = l[c];
        s && s.equals(h) || (e.push(h), s = h);
      }
    }
    return this.autoClose && e.length > 1 && !e[e.length - 1].equals(e[0]) && e.push(e[0]), e;
  }
  copy(t) {
    super.copy(t), this.curves = [];
    for (let e = 0, s = t.curves.length; e < s; e++) {
      const i = t.curves[e];
      this.curves.push(i.clone());
    }
    return this.autoClose = t.autoClose, this;
  }
  toJSON() {
    const t = super.toJSON();
    t.autoClose = this.autoClose, t.curves = [];
    for (let e = 0, s = this.curves.length; e < s; e++) {
      const i = this.curves[e];
      t.curves.push(i.toJSON());
    }
    return t;
  }
  fromJSON(t) {
    super.fromJSON(t), this.autoClose = t.autoClose, this.curves = [];
    for (let e = 0, s = t.curves.length; e < s; e++) {
      const i = t.curves[e];
      this.curves.push(new Na[i.type]().fromJSON(i));
    }
    return this;
  }
}
class Xr extends bu {
  constructor(t) {
    super(), this.type = "Path", this.currentPoint = new v(), t && this.setFromPoints(t);
  }
  setFromPoints(t) {
    this.moveTo(t[0].x, t[0].y);
    for (let e = 1, s = t.length; e < s; e++)
      this.lineTo(t[e].x, t[e].y);
    return this;
  }
  moveTo(t, e) {
    return this.currentPoint.set(t, e), this;
  }
  lineTo(t, e) {
    const s = new Gc(this.currentPoint.clone(), new v(t, e));
    return this.curves.push(s), this.currentPoint.set(t, e), this;
  }
  quadraticCurveTo(t, e, s, i) {
    const n = new Vc(
      this.currentPoint.clone(),
      new v(t, e),
      new v(s, i)
    );
    return this.curves.push(n), this.currentPoint.set(s, i), this;
  }
  bezierCurveTo(t, e, s, i, n, r) {
    const o = new Zc(
      this.currentPoint.clone(),
      new v(t, e),
      new v(s, i),
      new v(n, r)
    );
    return this.curves.push(o), this.currentPoint.set(n, r), this;
  }
  splineThru(t) {
    const e = [this.currentPoint.clone()].concat(t), s = new Mc(e);
    return this.curves.push(s), this.currentPoint.copy(t[t.length - 1]), this;
  }
  arc(t, e, s, i, n, r) {
    const o = this.currentPoint.x, l = this.currentPoint.y;
    return this.absarc(
      t + o,
      e + l,
      s,
      i,
      n,
      r
    ), this;
  }
  absarc(t, e, s, i, n, r) {
    return this.absellipse(t, e, s, s, i, n, r), this;
  }
  ellipse(t, e, s, i, n, r, o, l) {
    const c = this.currentPoint.x, h = this.currentPoint.y;
    return this.absellipse(t + c, e + h, s, i, n, r, o, l), this;
  }
  absellipse(t, e, s, i, n, r, o, l) {
    const c = new Ae(t, e, s, i, n, r, o, l);
    if (this.curves.length > 0) {
      const u = c.getPoint(0);
      u.equals(this.currentPoint) || this.lineTo(u.x, u.y);
    }
    this.curves.push(c);
    const h = c.getPoint(1);
    return this.currentPoint.copy(h), this;
  }
  copy(t) {
    return super.copy(t), this.currentPoint.copy(t.currentPoint), this;
  }
  toJSON() {
    const t = super.toJSON();
    return t.currentPoint = this.currentPoint.toArray(), t;
  }
  fromJSON(t) {
    return super.fromJSON(t), this.currentPoint.fromArray(t.currentPoint), this;
  }
}
class te extends Xr {
  constructor(t) {
    super(t), this.uuid = Ve(), this.type = "Shape", this.holes = [];
  }
  getPointsHoles(t) {
    const e = [];
    for (let s = 0, i = this.holes.length; s < i; s++)
      e[s] = this.holes[s].getPoints(t);
    return e;
  }
  // get points of shape and holes (keypoints based on segments parameter)
  extractPoints(t) {
    return {
      shape: this.getPoints(t),
      holes: this.getPointsHoles(t)
    };
  }
  copy(t) {
    super.copy(t), this.holes = [];
    for (let e = 0, s = t.holes.length; e < s; e++) {
      const i = t.holes[e];
      this.holes.push(i.clone());
    }
    return this;
  }
  toJSON() {
    const t = super.toJSON();
    t.uuid = this.uuid, t.holes = [];
    for (let e = 0, s = this.holes.length; e < s; e++) {
      const i = this.holes[e];
      t.holes.push(i.toJSON());
    }
    return t;
  }
  fromJSON(t) {
    super.fromJSON(t), this.uuid = t.uuid, this.holes = [];
    for (let e = 0, s = t.holes.length; e < s; e++) {
      const i = t.holes[e];
      this.holes.push(new Xr().fromJSON(i));
    }
    return this;
  }
}
const yu = {
  triangulate: function(a, t, e = 2) {
    const s = t && t.length, i = s ? t[0] * e : a.length;
    let n = Lc(a, 0, i, e, !0);
    const r = [];
    if (!n || n.next === n.prev) return r;
    let o, l, c, h, u, d, p;
    if (s && (n = Zu(a, t, n, e)), a.length > 80 * e) {
      o = c = a[0], l = h = a[1];
      for (let m = e; m < i; m += e)
        u = a[m], d = a[m + 1], u < o && (o = u), d < l && (l = d), u > c && (c = u), d > h && (h = d);
      p = Math.max(c - o, h - l), p = p !== 0 ? 32767 / p : 0;
    }
    return Ue(n, r, e, o, l, p, 0), r;
  }
};
function Lc(a, t, e, s, i) {
  let n, r;
  if (i === zu(a, t, e, s) > 0)
    for (n = t; n < e; n += s) r = _a(n, a[n], a[n + 1], r);
  else
    for (n = e - s; n >= t; n -= s) r = _a(n, a[n], a[n + 1], r);
  return r && Xs(r, r.next) && (_e(r), r = r.next), r;
}
function se(a, t) {
  if (!a) return a;
  t || (t = a);
  let e = a, s;
  do
    if (s = !1, !e.steiner && (Xs(e, e.next) || U(e.prev, e, e.next) === 0)) {
      if (_e(e), e = t = e.prev, e === e.next) break;
      s = !0;
    } else
      e = e.next;
  while (s || e !== t);
  return t;
}
function Ue(a, t, e, s, i, n, r) {
  if (!a) return;
  !r && n && wu(a, s, i, n);
  let o = a, l, c;
  for (; a.prev !== a.next; ) {
    if (l = a.prev, c = a.next, n ? gu(a, s, i, n) : xu(a)) {
      t.push(l.i / e | 0), t.push(a.i / e | 0), t.push(c.i / e | 0), _e(a), a = c.next, o = c.next;
      continue;
    }
    if (a = c, a === o) {
      r ? r === 1 ? (a = Ru(se(a), t, e), Ue(a, t, e, s, i, n, 2)) : r === 2 && Su(a, t, e, s, i, n) : Ue(se(a), t, e, s, i, n, 1);
      break;
    }
  }
}
function xu(a) {
  const t = a.prev, e = a, s = a.next;
  if (U(t, e, s) >= 0) return !1;
  const i = t.x, n = e.x, r = s.x, o = t.y, l = e.y, c = s.y, h = i < n ? i < r ? i : r : n < r ? n : r, u = o < l ? o < c ? o : c : l < c ? l : c, d = i > n ? i > r ? i : r : n > r ? n : r, p = o > l ? o > c ? o : c : l > c ? l : c;
  let m = s.next;
  for (; m !== t; ) {
    if (m.x >= h && m.x <= d && m.y >= u && m.y <= p && Re(i, o, n, l, r, c, m.x, m.y) && U(m.prev, m, m.next) >= 0) return !1;
    m = m.next;
  }
  return !0;
}
function gu(a, t, e, s) {
  const i = a.prev, n = a, r = a.next;
  if (U(i, n, r) >= 0) return !1;
  const o = i.x, l = n.x, c = r.x, h = i.y, u = n.y, d = r.y, p = o < l ? o < c ? o : c : l < c ? l : c, m = h < u ? h < d ? h : d : u < d ? u : d, f = o > l ? o > c ? o : c : l > c ? l : c, b = h > u ? h > d ? h : d : u > d ? u : d, y = zr(p, m, t, e, s), x = zr(f, b, t, e, s);
  let g = a.prevZ, S = a.nextZ;
  for (; g && g.z >= y && S && S.z <= x; ) {
    if (g.x >= p && g.x <= f && g.y >= m && g.y <= b && g !== i && g !== r && Re(o, h, l, u, c, d, g.x, g.y) && U(g.prev, g, g.next) >= 0 || (g = g.prevZ, S.x >= p && S.x <= f && S.y >= m && S.y <= b && S !== i && S !== r && Re(o, h, l, u, c, d, S.x, S.y) && U(S.prev, S, S.next) >= 0)) return !1;
    S = S.nextZ;
  }
  for (; g && g.z >= y; ) {
    if (g.x >= p && g.x <= f && g.y >= m && g.y <= b && g !== i && g !== r && Re(o, h, l, u, c, d, g.x, g.y) && U(g.prev, g, g.next) >= 0) return !1;
    g = g.prevZ;
  }
  for (; S && S.z <= x; ) {
    if (S.x >= p && S.x <= f && S.y >= m && S.y <= b && S !== i && S !== r && Re(o, h, l, u, c, d, S.x, S.y) && U(S.prev, S, S.next) >= 0) return !1;
    S = S.nextZ;
  }
  return !0;
}
function Ru(a, t, e) {
  let s = a;
  do {
    const i = s.prev, n = s.next.next;
    !Xs(i, n) && wc(i, s, s.next, n) && Ne(i, n) && Ne(n, i) && (t.push(i.i / e | 0), t.push(s.i / e | 0), t.push(n.i / e | 0), _e(s), _e(s.next), s = a = n), s = s.next;
  } while (s !== a);
  return se(s);
}
function Su(a, t, e, s, i, n) {
  let r = a;
  do {
    let o = r.next.next;
    for (; o !== r.prev; ) {
      if (r.i !== o.i && ku(r, o)) {
        let l = vc(r, o);
        r = se(r, r.next), l = se(l, l.next), Ue(r, t, e, s, i, n, 0), Ue(l, t, e, s, i, n, 0);
        return;
      }
      o = o.next;
    }
    r = r.next;
  } while (r !== a);
}
function Zu(a, t, e, s) {
  const i = [];
  let n, r, o, l, c;
  for (n = 0, r = t.length; n < r; n++)
    o = t[n] * s, l = n < r - 1 ? t[n + 1] * s : a.length, c = Lc(a, o, l, s, !1), c === c.next && (c.steiner = !0), i.push(Fu(c));
  for (i.sort(Gu), n = 0; n < i.length; n++)
    e = Vu(i[n], e);
  return e;
}
function Gu(a, t) {
  return a.x - t.x;
}
function Vu(a, t) {
  const e = Mu(a, t);
  if (!e)
    return t;
  const s = vc(e, a);
  return se(s, s.next), se(e, e.next);
}
function Mu(a, t) {
  let e = t, s = -1 / 0, i;
  const n = a.x, r = a.y;
  do {
    if (r <= e.y && r >= e.next.y && e.next.y !== e.y) {
      const d = e.x + (r - e.y) * (e.next.x - e.x) / (e.next.y - e.y);
      if (d <= n && d > s && (s = d, i = e.x < e.next.x ? e : e.next, d === n))
        return i;
    }
    e = e.next;
  } while (e !== t);
  if (!i) return null;
  const o = i, l = i.x, c = i.y;
  let h = 1 / 0, u;
  e = i;
  do
    n >= e.x && e.x >= l && n !== e.x && Re(r < c ? n : s, r, l, c, r < c ? s : n, r, e.x, e.y) && (u = Math.abs(r - e.y) / (n - e.x), Ne(e, a) && (u < h || u === h && (e.x > i.x || e.x === i.x && Lu(i, e))) && (i = e, h = u)), e = e.next;
  while (e !== o);
  return i;
}
function Lu(a, t) {
  return U(a.prev, a, t.prev) < 0 && U(t.next, a, a.next) < 0;
}
function wu(a, t, e, s) {
  let i = a;
  do
    i.z === 0 && (i.z = zr(i.x, i.y, t, e, s)), i.prevZ = i.prev, i.nextZ = i.next, i = i.next;
  while (i !== a);
  i.prevZ.nextZ = null, i.prevZ = null, vu(i);
}
function vu(a) {
  let t, e, s, i, n, r, o, l, c = 1;
  do {
    for (e = a, a = null, n = null, r = 0; e; ) {
      for (r++, s = e, o = 0, t = 0; t < c && (o++, s = s.nextZ, !!s); t++)
        ;
      for (l = c; o > 0 || l > 0 && s; )
        o !== 0 && (l === 0 || !s || e.z <= s.z) ? (i = e, e = e.nextZ, o--) : (i = s, s = s.nextZ, l--), n ? n.nextZ = i : a = i, i.prevZ = n, n = i;
      e = s;
    }
    n.nextZ = null, c *= 2;
  } while (r > 1);
  return a;
}
function zr(a, t, e, s, i) {
  return a = (a - e) * i | 0, t = (t - s) * i | 0, a = (a | a << 8) & 16711935, a = (a | a << 4) & 252645135, a = (a | a << 2) & 858993459, a = (a | a << 1) & 1431655765, t = (t | t << 8) & 16711935, t = (t | t << 4) & 252645135, t = (t | t << 2) & 858993459, t = (t | t << 1) & 1431655765, a | t << 1;
}
function Fu(a) {
  let t = a, e = a;
  do
    (t.x < e.x || t.x === e.x && t.y < e.y) && (e = t), t = t.next;
  while (t !== a);
  return e;
}
function Re(a, t, e, s, i, n, r, o) {
  return (i - r) * (t - o) >= (a - r) * (n - o) && (a - r) * (s - o) >= (e - r) * (t - o) && (e - r) * (n - o) >= (i - r) * (s - o);
}
function ku(a, t) {
  return a.next.i !== t.i && a.prev.i !== t.i && !Tu(a, t) && // dones't intersect other edges
  (Ne(a, t) && Ne(t, a) && Xu(a, t) && // locally visible
  (U(a.prev, a, t.prev) || U(a, t.prev, t)) || // does not create opposite-facing sectors
  Xs(a, t) && U(a.prev, a, a.next) > 0 && U(t.prev, t, t.next) > 0);
}
function U(a, t, e) {
  return (t.y - a.y) * (e.x - t.x) - (t.x - a.x) * (e.y - t.y);
}
function Xs(a, t) {
  return a.x === t.x && a.y === t.y;
}
function wc(a, t, e, s) {
  const i = Ss(U(a, t, e)), n = Ss(U(a, t, s)), r = Ss(U(e, s, a)), o = Ss(U(e, s, t));
  return !!(i !== n && r !== o || i === 0 && Rs(a, e, t) || n === 0 && Rs(a, s, t) || r === 0 && Rs(e, a, s) || o === 0 && Rs(e, t, s));
}
function Rs(a, t, e) {
  return t.x <= Math.max(a.x, e.x) && t.x >= Math.min(a.x, e.x) && t.y <= Math.max(a.y, e.y) && t.y >= Math.min(a.y, e.y);
}
function Ss(a) {
  return a > 0 ? 1 : a < 0 ? -1 : 0;
}
function Tu(a, t) {
  let e = a;
  do {
    if (e.i !== a.i && e.next.i !== a.i && e.i !== t.i && e.next.i !== t.i && wc(e, e.next, a, t)) return !0;
    e = e.next;
  } while (e !== a);
  return !1;
}
function Ne(a, t) {
  return U(a.prev, a, a.next) < 0 ? U(a, t, a.next) >= 0 && U(a, a.prev, t) >= 0 : U(a, t, a.prev) < 0 || U(a, a.next, t) < 0;
}
function Xu(a, t) {
  let e = a, s = !1;
  const i = (a.x + t.x) / 2, n = (a.y + t.y) / 2;
  do
    e.y > n != e.next.y > n && e.next.y !== e.y && i < (e.next.x - e.x) * (n - e.y) / (e.next.y - e.y) + e.x && (s = !s), e = e.next;
  while (e !== a);
  return s;
}
function vc(a, t) {
  const e = new Wr(a.i, a.x, a.y), s = new Wr(t.i, t.x, t.y), i = a.next, n = t.prev;
  return a.next = t, t.prev = a, e.next = i, i.prev = e, s.next = e, e.prev = s, n.next = s, s.prev = n, s;
}
function _a(a, t, e, s) {
  const i = new Wr(a, t, e);
  return s ? (i.next = s.next, i.prev = s, s.next.prev = i, s.next = i) : (i.prev = i, i.next = i), i;
}
function _e(a) {
  a.next.prev = a.prev, a.prev.next = a.next, a.prevZ && (a.prevZ.nextZ = a.nextZ), a.nextZ && (a.nextZ.prevZ = a.prevZ);
}
function Wr(a, t, e) {
  this.i = a, this.x = t, this.y = e, this.prev = null, this.next = null, this.z = 0, this.prevZ = null, this.nextZ = null, this.steiner = !1;
}
function zu(a, t, e, s) {
  let i = 0;
  for (let n = t, r = e - s; n < e; n += s)
    i += (a[r] - a[n]) * (a[n + 1] + a[r + 1]), r = n;
  return i;
}
class Ge {
  // calculate area of the contour polygon
  static area(t) {
    const e = t.length;
    let s = 0;
    for (let i = e - 1, n = 0; n < e; i = n++)
      s += t[i].x * t[n].y - t[n].x * t[i].y;
    return s * 0.5;
  }
  static isClockWise(t) {
    return Ge.area(t) < 0;
  }
  static triangulateShape(t, e) {
    const s = [], i = [], n = [];
    Ha(t), Ya(s, t);
    let r = t.length;
    e.forEach(Ha);
    for (let l = 0; l < e.length; l++)
      i.push(r), r += e[l].length, Ya(s, e[l]);
    const o = yu.triangulate(s, i);
    for (let l = 0; l < o.length; l += 3)
      n.push(o.slice(l, l + 3));
    return n;
  }
}
function Ha(a) {
  const t = a.length;
  t > 2 && a[t - 1].equals(a[0]) && a.pop();
}
function Ya(a, t) {
  for (let e = 0; e < t.length; e++)
    a.push(t[e].x), a.push(t[e].y);
}
class Pr extends rt {
  constructor(t = 1, e = 1, s = 1, i = 1) {
    super(), this.type = "PlaneGeometry", this.parameters = {
      width: t,
      height: e,
      widthSegments: s,
      heightSegments: i
    };
    const n = t / 2, r = e / 2, o = Math.floor(s), l = Math.floor(i), c = o + 1, h = l + 1, u = t / o, d = e / l, p = [], m = [], f = [], b = [];
    for (let y = 0; y < h; y++) {
      const x = y * d - r;
      for (let g = 0; g < c; g++) {
        const S = g * u - n;
        m.push(S, -x, 0), f.push(0, 0, 1), b.push(g / o), b.push(1 - y / l);
      }
    }
    for (let y = 0; y < l; y++)
      for (let x = 0; x < o; x++) {
        const g = x + c * y, S = x + c * (y + 1), Z = x + 1 + c * (y + 1), V = x + 1 + c * y;
        p.push(g, S, V), p.push(S, Z, V);
      }
    this.setIndex(p), this.setAttribute("position", new ht(m, 3)), this.setAttribute("normal", new ht(f, 3)), this.setAttribute("uv", new ht(b, 2));
  }
  copy(t) {
    return super.copy(t), this.parameters = Object.assign({}, t.parameters), this;
  }
  static fromJSON(t) {
    return new Pr(t.width, t.height, t.widthSegments, t.heightSegments);
  }
}
class Ke extends rt {
  constructor(t = new te([new v(0, 0.5), new v(-0.5, -0.5), new v(0.5, -0.5)]), e = 12) {
    super(), this.type = "ShapeGeometry", this.parameters = {
      shapes: t,
      curveSegments: e
    };
    const s = [], i = [], n = [], r = [];
    let o = 0, l = 0;
    if (Array.isArray(t) === !1)
      c(t);
    else
      for (let h = 0; h < t.length; h++)
        c(t[h]), this.addGroup(o, l, h), o += l, l = 0;
    this.setIndex(s), this.setAttribute("position", new ht(i, 3)), this.setAttribute("normal", new ht(n, 3)), this.setAttribute("uv", new ht(r, 2));
    function c(h) {
      const u = i.length / 3, d = h.extractPoints(e);
      let p = d.shape;
      const m = d.holes;
      Ge.isClockWise(p) === !1 && (p = p.reverse());
      for (let b = 0, y = m.length; b < y; b++) {
        const x = m[b];
        Ge.isClockWise(x) === !0 && (m[b] = x.reverse());
      }
      const f = Ge.triangulateShape(p, m);
      for (let b = 0, y = m.length; b < y; b++) {
        const x = m[b];
        p = p.concat(x);
      }
      for (let b = 0, y = p.length; b < y; b++) {
        const x = p[b];
        i.push(x.x, x.y, 0), n.push(0, 0, 1), r.push(x.x, x.y);
      }
      for (let b = 0, y = f.length; b < y; b++) {
        const x = f[b], g = x[0] + u, S = x[1] + u, Z = x[2] + u;
        s.push(g, S, Z), l += 3;
      }
    }
  }
  copy(t) {
    return super.copy(t), this.parameters = Object.assign({}, t.parameters), this;
  }
  toJSON() {
    const t = super.toJSON(), e = this.parameters.shapes;
    return Wu(e, t);
  }
  static fromJSON(t, e) {
    const s = [];
    for (let i = 0, n = t.shapes.length; i < n; i++) {
      const r = e[t.shapes[i]];
      s.push(r);
    }
    return new Ke(s, t.curveSegments);
  }
}
function Wu(a, t) {
  if (t.shapes = [], Array.isArray(a))
    for (let e = 0, s = a.length; e < s; e++) {
      const i = a[e];
      t.shapes.push(i.uuid);
    }
  else
    t.shapes.push(a.uuid);
  return t;
}
class Iu extends Ur {
  constructor(t) {
    super(), this.isMeshStandardMaterial = !0, this.type = "MeshStandardMaterial", this.defines = { STANDARD: "" }, this.color = new Jt(16777215), this.roughness = 1, this.metalness = 0, this.map = null, this.lightMap = null, this.lightMapIntensity = 1, this.aoMap = null, this.aoMapIntensity = 1, this.emissive = new Jt(0), this.emissiveIntensity = 1, this.emissiveMap = null, this.bumpMap = null, this.bumpScale = 1, this.normalMap = null, this.normalMapType = 0, this.normalScale = new v(1, 1), this.displacementMap = null, this.displacementScale = 1, this.displacementBias = 0, this.roughnessMap = null, this.metalnessMap = null, this.alphaMap = null, this.envMap = null, this.envMapRotation = new Me(), this.envMapIntensity = 1, this.wireframe = !1, this.wireframeLinewidth = 1, this.wireframeLinecap = "round", this.wireframeLinejoin = "round", this.flatShading = !1, this.fog = !0, this.setValues(t);
  }
  copy(t) {
    return super.copy(t), this.defines = { STANDARD: "" }, this.color.copy(t.color), this.roughness = t.roughness, this.metalness = t.metalness, this.map = t.map, this.lightMap = t.lightMap, this.lightMapIntensity = t.lightMapIntensity, this.aoMap = t.aoMap, this.aoMapIntensity = t.aoMapIntensity, this.emissive.copy(t.emissive), this.emissiveMap = t.emissiveMap, this.emissiveIntensity = t.emissiveIntensity, this.bumpMap = t.bumpMap, this.bumpScale = t.bumpScale, this.normalMap = t.normalMap, this.normalMapType = t.normalMapType, this.normalScale.copy(t.normalScale), this.displacementMap = t.displacementMap, this.displacementScale = t.displacementScale, this.displacementBias = t.displacementBias, this.roughnessMap = t.roughnessMap, this.metalnessMap = t.metalnessMap, this.alphaMap = t.alphaMap, this.envMap = t.envMap, this.envMapRotation.copy(t.envMapRotation), this.envMapIntensity = t.envMapIntensity, this.wireframe = t.wireframe, this.wireframeLinewidth = t.wireframeLinewidth, this.wireframeLinecap = t.wireframeLinecap, this.wireframeLinejoin = t.wireframeLinejoin, this.flatShading = t.flatShading, this.fog = t.fog, this;
  }
}
class Cu extends _r {
  constructor(t) {
    super(), this.isLineDashedMaterial = !0, this.type = "LineDashedMaterial", this.scale = 1, this.dashSize = 3, this.gapSize = 1, this.setValues(t);
  }
  copy(t) {
    return super.copy(t), this.scale = t.scale, this.dashSize = t.dashSize, this.gapSize = t.gapSize, this;
  }
}
const Pa = {
  enabled: !1,
  files: {},
  add: function(a, t) {
    this.enabled !== !1 && (this.files[a] = t);
  },
  get: function(a) {
    if (this.enabled !== !1)
      return this.files[a];
  },
  remove: function(a) {
    delete this.files[a];
  },
  clear: function() {
    this.files = {};
  }
};
class Eu {
  constructor(t, e, s) {
    const i = this;
    let n = !1, r = 0, o = 0, l;
    const c = [];
    this.onStart = void 0, this.onLoad = t, this.onProgress = e, this.onError = s, this.itemStart = function(h) {
      o++, n === !1 && i.onStart !== void 0 && i.onStart(h, r, o), n = !0;
    }, this.itemEnd = function(h) {
      r++, i.onProgress !== void 0 && i.onProgress(h, r, o), r === o && (n = !1, i.onLoad !== void 0 && i.onLoad());
    }, this.itemError = function(h) {
      i.onError !== void 0 && i.onError(h);
    }, this.resolveURL = function(h) {
      return l ? l(h) : h;
    }, this.setURLModifier = function(h) {
      return l = h, this;
    }, this.addHandler = function(h, u) {
      return c.push(h, u), this;
    }, this.removeHandler = function(h) {
      const u = c.indexOf(h);
      return u !== -1 && c.splice(u, 2), this;
    }, this.getHandler = function(h) {
      for (let u = 0, d = c.length; u < d; u += 2) {
        const p = c[u], m = c[u + 1];
        if (p.global && (p.lastIndex = 0), p.test(h))
          return m;
      }
      return null;
    };
  }
}
const Bu = /* @__PURE__ */ new Eu();
class Ar {
  constructor(t) {
    this.manager = t !== void 0 ? t : Bu, this.crossOrigin = "anonymous", this.withCredentials = !1, this.path = "", this.resourcePath = "", this.requestHeader = {};
  }
  load() {
  }
  loadAsync(t, e) {
    const s = this;
    return new Promise(function(i, n) {
      s.load(t, i, e, n);
    });
  }
  parse() {
  }
  setCrossOrigin(t) {
    return this.crossOrigin = t, this;
  }
  setWithCredentials(t) {
    return this.withCredentials = t, this;
  }
  setPath(t) {
    return this.path = t, this;
  }
  setResourcePath(t) {
    return this.resourcePath = t, this;
  }
  setRequestHeader(t) {
    return this.requestHeader = t, this;
  }
}
Ar.DEFAULT_MATERIAL_NAME = "__DEFAULT";
const kt = {};
class Uu extends Error {
  constructor(t, e) {
    super(t), this.response = e;
  }
}
class Nu extends Ar {
  constructor(t) {
    super(t);
  }
  load(t, e, s, i) {
    t === void 0 && (t = ""), this.path !== void 0 && (t = this.path + t), t = this.manager.resolveURL(t);
    const n = Pa.get(t);
    if (n !== void 0)
      return this.manager.itemStart(t), setTimeout(() => {
        e && e(n), this.manager.itemEnd(t);
      }, 0), n;
    if (kt[t] !== void 0) {
      kt[t].push({
        onLoad: e,
        onProgress: s,
        onError: i
      });
      return;
    }
    kt[t] = [], kt[t].push({
      onLoad: e,
      onProgress: s,
      onError: i
    });
    const r = new Request(t, {
      headers: new Headers(this.requestHeader),
      credentials: this.withCredentials ? "include" : "same-origin"
      // An abort controller could be added within a future PR
    }), o = this.mimeType, l = this.responseType;
    fetch(r).then((c) => {
      if (c.status === 200 || c.status === 0) {
        if (c.status === 0 && console.warn("THREE.FileLoader: HTTP Status 0 received."), typeof ReadableStream > "u" || c.body === void 0 || c.body.getReader === void 0)
          return c;
        const h = kt[t], u = c.body.getReader(), d = c.headers.get("X-File-Size") || c.headers.get("Content-Length"), p = d ? parseInt(d) : 0, m = p !== 0;
        let f = 0;
        const b = new ReadableStream({
          start(y) {
            x();
            function x() {
              u.read().then(({ done: g, value: S }) => {
                if (g)
                  y.close();
                else {
                  f += S.byteLength;
                  const Z = new ProgressEvent("progress", { lengthComputable: m, loaded: f, total: p });
                  for (let V = 0, G = h.length; V < G; V++) {
                    const M = h[V];
                    M.onProgress && M.onProgress(Z);
                  }
                  y.enqueue(S), x();
                }
              }, (g) => {
                y.error(g);
              });
            }
          }
        });
        return new Response(b);
      } else
        throw new Uu(`fetch for "${c.url}" responded with ${c.status}: ${c.statusText}`, c);
    }).then((c) => {
      switch (l) {
        case "arraybuffer":
          return c.arrayBuffer();
        case "blob":
          return c.blob();
        case "document":
          return c.text().then((h) => new DOMParser().parseFromString(h, o));
        case "json":
          return c.json();
        default:
          if (o === void 0)
            return c.text();
          {
            const u = /charset="?([^;"\s]*)"?/i.exec(o), d = u && u[1] ? u[1].toLowerCase() : void 0, p = new TextDecoder(d);
            return c.arrayBuffer().then((m) => p.decode(m));
          }
      }
    }).then((c) => {
      Pa.add(t, c);
      const h = kt[t];
      delete kt[t];
      for (let u = 0, d = h.length; u < d; u++) {
        const p = h[u];
        p.onLoad && p.onLoad(c);
      }
    }).catch((c) => {
      const h = kt[t];
      if (h === void 0)
        throw this.manager.itemError(t), c;
      delete kt[t];
      for (let u = 0, d = h.length; u < d; u++) {
        const p = h[u];
        p.onError && p.onError(c);
      }
      this.manager.itemError(t);
    }).finally(() => {
      this.manager.itemEnd(t);
    }), this.manager.itemStart(t);
  }
  setResponseType(t) {
    return this.responseType = t, this;
  }
  setMimeType(t) {
    return this.mimeType = t, this;
  }
}
const Aa = /* @__PURE__ */ new nt();
let Fc = class {
  constructor(t, e, s = 0, i = 1 / 0) {
    this.ray = new Br(t, e), this.near = s, this.far = i, this.camera = null, this.layers = new Rc(), this.params = {
      Mesh: {},
      Line: { threshold: 1 },
      LOD: {},
      Points: { threshold: 1 },
      Sprite: {}
    };
  }
  set(t, e) {
    this.ray.set(t, e);
  }
  setFromCamera(t, e) {
    e.isPerspectiveCamera ? (this.ray.origin.setFromMatrixPosition(e.matrixWorld), this.ray.direction.set(t.x, t.y, 0.5).unproject(e).sub(this.ray.origin).normalize(), this.camera = e) : e.isOrthographicCamera ? (this.ray.origin.set(t.x, t.y, (e.near + e.far) / (e.near - e.far)).unproject(e), this.ray.direction.set(0, 0, -1).transformDirection(e.matrixWorld), this.camera = e) : console.error("THREE.Raycaster: Unsupported camera type: " + e.type);
  }
  setFromXRController(t) {
    return Aa.identity().extractRotation(t.matrixWorld), this.ray.origin.setFromMatrixPosition(t.matrixWorld), this.ray.direction.set(0, 0, -1).applyMatrix4(Aa), this;
  }
  intersectObject(t, e = !0, s = []) {
    return Ir(t, this, s, e), s.sort(Ka), s;
  }
  intersectObjects(t, e = !0, s = []) {
    for (let i = 0, n = t.length; i < n; i++)
      Ir(t[i], this, s, e);
    return s.sort(Ka), s;
  }
};
function Ka(a, t) {
  return a.distance - t.distance;
}
function Ir(a, t, e, s) {
  let i = !0;
  if (a.layers.test(t.layers) && a.raycast(t, e) === !1 && (i = !1), i === !0 && s === !0) {
    const n = a.children;
    for (let r = 0, o = n.length; r < o; r++)
      Ir(n[r], t, e, !0);
  }
}
class _u {
  constructor() {
    this.type = "ShapePath", this.color = new Jt(), this.subPaths = [], this.currentPath = null;
  }
  moveTo(t, e) {
    return this.currentPath = new Xr(), this.subPaths.push(this.currentPath), this.currentPath.moveTo(t, e), this;
  }
  lineTo(t, e) {
    return this.currentPath.lineTo(t, e), this;
  }
  quadraticCurveTo(t, e, s, i) {
    return this.currentPath.quadraticCurveTo(t, e, s, i), this;
  }
  bezierCurveTo(t, e, s, i, n, r) {
    return this.currentPath.bezierCurveTo(t, e, s, i, n, r), this;
  }
  splineThru(t) {
    return this.currentPath.splineThru(t), this;
  }
  toShapes(t) {
    function e(y) {
      const x = [];
      for (let g = 0, S = y.length; g < S; g++) {
        const Z = y[g], V = new te();
        V.curves = Z.curves, x.push(V);
      }
      return x;
    }
    function s(y, x) {
      const g = x.length;
      let S = !1;
      for (let Z = g - 1, V = 0; V < g; Z = V++) {
        let G = x[Z], M = x[V], L = M.x - G.x, k = M.y - G.y;
        if (Math.abs(k) > Number.EPSILON) {
          if (k < 0 && (G = x[V], L = -L, M = x[Z], k = -k), y.y < G.y || y.y > M.y) continue;
          if (y.y === G.y) {
            if (y.x === G.x) return !0;
          } else {
            const w = k * (y.x - G.x) - L * (y.y - G.y);
            if (w === 0) return !0;
            if (w < 0) continue;
            S = !S;
          }
        } else {
          if (y.y !== G.y) continue;
          if (M.x <= y.x && y.x <= G.x || G.x <= y.x && y.x <= M.x) return !0;
        }
      }
      return S;
    }
    const i = Ge.isClockWise, n = this.subPaths;
    if (n.length === 0) return [];
    let r, o, l;
    const c = [];
    if (n.length === 1)
      return o = n[0], l = new te(), l.curves = o.curves, c.push(l), c;
    let h = !i(n[0].getPoints());
    h = t ? !h : h;
    const u = [], d = [];
    let p = [], m = 0, f;
    d[m] = void 0, p[m] = [];
    for (let y = 0, x = n.length; y < x; y++)
      o = n[y], f = o.getPoints(), r = i(f), r = t ? !r : r, r ? (!h && d[m] && m++, d[m] = { s: new te(), p: f }, d[m].s.curves = o.curves, h && m++, p[m] = []) : p[m].push({ h: o, p: f[0] });
    if (!d[0]) return e(n);
    if (d.length > 1) {
      let y = !1, x = 0;
      for (let g = 0, S = d.length; g < S; g++)
        u[g] = [];
      for (let g = 0, S = d.length; g < S; g++) {
        const Z = p[g];
        for (let V = 0; V < Z.length; V++) {
          const G = Z[V];
          let M = !0;
          for (let L = 0; L < d.length; L++)
            s(G.p, d[L].p) && (g !== L && x++, M ? (M = !1, u[L].push(G)) : y = !0);
          M && u[g].push(G);
        }
      }
      x > 0 && y === !1 && (p = u);
    }
    let b;
    for (let y = 0, x = d.length; y < x; y++) {
      l = d[y].s, c.push(l), b = p[y];
      for (let g = 0, S = b.length; g < S; g++)
        l.holes.push(b[g].h);
    }
    return c;
  }
}
typeof __THREE_DEVTOOLS__ < "u" && __THREE_DEVTOOLS__.dispatchEvent(new CustomEvent("register", { detail: {
  revision: xc
} }));
typeof window < "u" && (window.__THREE__ ? console.warn("WARNING: Multiple instances of Three.js being imported.") : window.__THREE__ = xc);
class Hu extends Ar {
  constructor(t) {
    super(t);
  }
  load(t, e, s, i) {
    const n = this, r = new Nu(this.manager);
    r.setPath(this.path), r.setRequestHeader(this.requestHeader), r.setWithCredentials(this.withCredentials), r.load(t, function(o) {
      const l = n.parse(JSON.parse(o));
      e && e(l);
    }, s, i);
  }
  parse(t) {
    return new Yu(t);
  }
}
class Yu {
  constructor(t) {
    this.isFont = !0, this.type = "Font", this.data = t;
  }
  generateShapes(t, e = 100) {
    const s = [], i = Pu(t, e, this.data);
    for (let n = 0, r = i.length; n < r; n++)
      s.push(...i[n].toShapes());
    return s;
  }
}
function Pu(a, t, e) {
  const s = Array.from(a), i = t / e.resolution, n = (e.boundingBox.yMax - e.boundingBox.yMin + e.underlineThickness) * i, r = [];
  let o = 0, l = 0;
  for (let c = 0; c < s.length; c++) {
    const h = s[c];
    if (h === `
`)
      o = 0, l -= n;
    else {
      const u = Au(h, i, o, l, e);
      o += u.offsetX, r.push(u.path);
    }
  }
  return r;
}
function Au(a, t, e, s, i) {
  const n = i.glyphs[a] || i.glyphs["?"];
  if (!n) {
    console.error('THREE.Font: character "' + a + '" does not exists in font family ' + i.familyName + ".");
    return;
  }
  const r = new _u();
  let o, l, c, h, u, d, p, m;
  if (n.o) {
    const f = n._cachedOutline || (n._cachedOutline = n.o.split(" "));
    for (let b = 0, y = f.length; b < y; )
      switch (f[b++]) {
        case "m":
          o = f[b++] * t + e, l = f[b++] * t + s, r.moveTo(o, l);
          break;
        case "l":
          o = f[b++] * t + e, l = f[b++] * t + s, r.lineTo(o, l);
          break;
        case "q":
          c = f[b++] * t + e, h = f[b++] * t + s, u = f[b++] * t + e, d = f[b++] * t + s, r.quadraticCurveTo(u, d, c, h);
          break;
        case "b":
          c = f[b++] * t + e, h = f[b++] * t + s, u = f[b++] * t + e, d = f[b++] * t + s, p = f[b++] * t + e, m = f[b++] * t + s, r.bezierCurveTo(u, d, p, m, c, h);
          break;
      }
  }
  return { offsetX: n.ha * t, path: r };
}
class P {
}
/** @property cache {boolean} Checks if module will cache generated entities or not */
Qt(P, "cache", !0), /** @property showFrozen {boolean} Checks if module will show frozen dxf entities */
Qt(P, "showFrozen", !1), /** @property showLocked {boolean} Checks if module will show locked dxf entities */
Qt(P, "showLocked", !0), /** @property paperSpace {int} also called  sheet or layout. 0 is always model. */
Qt(P, "paperSpace", 0), /** @property decimal {int} readed decimals in a number */
Qt(P, "decimals", 6), /** @property onBeforeTextDraw {method} An event to change text values before rendering it. Useful to change dimensions or text */
Qt(P, "onBeforeTextDraw", null);
class zs {
  constructor() {
    this.callbacks = [];
  }
  subscribe(t, e) {
    const s = this.callbacks.find((i) => i.name === t);
    typeof s < "u" ? s.callbacks.push(e) : this.callbacks.push({ name: t, callbacks: [e] });
  }
  unSubscribe(t, e) {
    const s = this.callbacks.find((i) => i.name === t);
    if (typeof s < "u") {
      let i = s.callbacks.indexOf(e);
      i > -1 && s.callbacks.splice(i, 1), s.callbacks.length === 0 && (i = this.callbacks.indexOf(s), i > -1 && this.callbacks.splice(i, 1));
    }
  }
  hasSubscribers(t) {
    return typeof this.callbacks.find((s) => s.name === t) < "u";
  }
  async trigger(t, ...e) {
    const s = this.callbacks.find((i) => i.name === t);
    if (typeof s < "u")
      for (const i of s.callbacks)
        i && i.constructor.name === "AsyncFunction" ? await i(...e) : i(...e);
  }
}
var xi = /* @__PURE__ */ new Map(), Ja = 1;
class Ku extends zs {
  constructor() {
    super(), this.cache = !0;
  }
  /**
   * Returns the cached object if exist. Otherwise null
   * @param entity {Entity} DXFViewer Entity.
      * @return {Object} object  usually composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  _getCached(t) {
    if (!t._cache || !P.cache) return null;
    const e = t._cache;
    if (xi.has(e)) {
      const s = xi.get(e).deref();
      if (s !== void 0)
        return s;
    }
    return null;
  }
  /**
   * Stores the entity in the cache. Adds _cache property to the entity to store the cache key 
   * @param entity {Entity} DXFViewer Entity.
   * @param model {Object} object to be stored. usually an object composed as {geometry: THREE.Geometry, material: THREE.Material}.
  */
  _setCache(t, e) {
    if (!P.cache) return;
    const s = "e" + Ja;
    t._cache = s, Ja++, xi.set(s, e);
  }
}
var gi = {};
class Kr {
  constructor() {
  }
  /**
   * Returns the entity's material. It will create a new material if it doesn't exist in the cache.
   * @param entity {Entity} DXFViewer Entity.
      * @param type {string} A flag to indicate the type of material to be created. It can be 'shape', 'line' or 'dashed'.
   * @param tables {Object} dxf tables object, conatining at least layers and ltypes.
      * @return {THREE.Material} Material
  */
  getMaterial(t, e, s) {
    let i = this._getColorHex(t, s.layers), n = t.lineTypeName + e + i;
    if (gi[n]) return gi[n];
    let r = e === "shape" ? new ks({ side: 2 }) : e === "line" ? new _r() : this._createDashedMaterial(t, s.ltypes);
    return r.color.setHex(i), r.color.convertSRGBToLinear(), r.name = n, gi[n] = r, r;
  }
  _createDashedMaterial(t, e) {
    let s = 4, i = 4, n = t.lineTypeName && Object.prototype.hasOwnProperty.call(e, t.lineTypeName) ? e[t.lineTypeName] : null;
    return i = n.pattern && n.pattern.length > 0 ? Math.max(...n.pattern.map((r) => r.length)) : 4, s = n.pattern && n.pattern.length > 0 ? n.pattern.map((r) => r.length === -1).length : 4, i = i === 0 ? 4 : i, new Cu({ gapSize: s, dashSize: i });
  }
  _getColorHex(t, e) {
    let s = t.fillColor ? t.fillColor : t.colorNumber;
    if (!s || s === 0) {
      let i = Object.prototype.hasOwnProperty.call(e, t.layer) ? e[t.layer] : null;
      if (i) return i.color;
    }
    return this.getColorByNumber(s);
  }
  /**
   * Returns hex color from color number. Based on DXF format table colors
   * @param colorNumber {Number} dxf colorNumber.
      * @return {Hexadecimal} color's hex number
  */
  getColorByNumber(t) {
    switch (t) {
      case 0:
        return 0;
      case 1:
        return 16711680;
      case 2:
        return 16776960;
      case 3:
        return 65280;
      case 4:
        return 65535;
      case 5:
        return 255;
      case 6:
        return 16711935;
      case 7:
        return 16777215;
      case 8:
        return 4276545;
      case 9:
        return 8421504;
      case 10:
        return 16711680;
      case 11:
        return 16755370;
      case 12:
        return 12386304;
      case 13:
        return 12418686;
      case 14:
        return 8454144;
      case 15:
        return 8476246;
      case 16:
        return 6815744;
      case 17:
        return 6833477;
      case 18:
        return 5177344;
      case 19:
        return 5190965;
      case 20:
        return 16727808;
      case 21:
        return 16760746;
      case 22:
        return 12398080;
      case 23:
        return 12422526;
      case 24:
        return 8462080;
      case 25:
        return 8478806;
      case 26:
        return 6822144;
      case 27:
        return 6835781;
      case 28:
        return 5182208;
      case 29:
        return 5192501;
      case 30:
        return 16744192;
      case 31:
        return 16766122;
      case 32:
        return 12410368;
      case 33:
        return 12426622;
      case 34:
        return 8470528;
      case 35:
        return 8481622;
      case 36:
        return 6829056;
      case 37:
        return 6837829;
      case 38:
        return 5187328;
      case 39:
        return 5194293;
      case 40:
        return 16760576;
      case 41:
        return 16771754;
      case 42:
        return 12422400;
      case 43:
        return 12430718;
      case 44:
        return 8478720;
      case 45:
        return 8484438;
      case 46:
        return 6835712;
      case 47:
        return 6840133;
      case 48:
        return 5192448;
      case 49:
        return 5196085;
      case 50:
        return 16776960;
      case 51:
        return 16777130;
      case 52:
        return 12434688;
      case 53:
        return 12434814;
      case 54:
        return 8487168;
      case 55:
        return 8487254;
      case 56:
        return 6842368;
      case 57:
        return 6842437;
      case 58:
        return 5197568;
      case 59:
        return 5197621;
      case 60:
        return 12582656;
      case 61:
        return 15400874;
      case 62:
        return 9288960;
      case 63:
        return 11386238;
      case 64:
        return 6324480;
      case 65:
        return 7766358;
      case 66:
        return 5138432;
      case 67:
        return 6252613;
      case 68:
        return 3886848;
      case 69:
        return 4804405;
      case 70:
        return 8388352;
      case 71:
        return 13959082;
      case 72:
        return 6208768;
      case 73:
        return 10337662;
      case 74:
        return 4227328;
      case 75:
        return 7045462;
      case 76:
        return 3434496;
      case 77:
        return 5662789;
      case 78:
        return 2576128;
      case 79:
        return 4345653;
      case 80:
        return 4194048;
      case 81:
        return 12582826;
      case 82:
        return 3063040;
      case 83:
        return 9289086;
      case 84:
        return 2064640;
      case 85:
        return 6324566;
      case 86:
        return 1665024;
      case 87:
        return 5138501;
      case 88:
        return 1265408;
      case 89:
        return 3886901;
      case 90:
        return 65280;
      case 91:
        return 11206570;
      case 92:
        return 48384;
      case 93:
        return 8306046;
      case 94:
        return 33024;
      case 95:
        return 5669206;
      case 96:
        return 26624;
      case 97:
        return 4548677;
      case 98:
        return 20224;
      case 99:
        return 3493685;
      case 100:
        return 65343;
      case 101:
        return 11206591;
      case 102:
        return 48430;
      case 103:
        return 8306061;
      case 104:
        return 33055;
      case 105:
        return 5669216;
      case 106:
        return 26649;
      case 107:
        return 4548686;
      case 108:
        return 20243;
      case 109:
        return 3493691;
      case 110:
        return 65407;
      case 111:
        return 11206612;
      case 112:
        return 48478;
      case 113:
        return 8306077;
      case 114:
        return 33088;
      case 115:
        return 5669227;
      case 116:
        return 26676;
      case 117:
        return 4548694;
      case 118:
        return 20263;
      case 119:
        return 3493698;
      case 120:
        return 65471;
      case 121:
        return 11206634;
      case 122:
        return 48525;
      case 123:
        return 8306093;
      case 124:
        return 33120;
      case 125:
        return 5669238;
      case 126:
        return 26702;
      case 127:
        return 4548703;
      case 128:
        return 20283;
      case 129:
        return 3493705;
      case 130:
        return 65535;
      case 131:
        return 11206655;
      case 132:
        return 48573;
      case 133:
        return 8306109;
      case 134:
        return 33153;
      case 135:
        return 5669249;
      case 136:
        return 26728;
      case 137:
        return 4548712;
      case 138:
        return 20303;
      case 139:
        return 3493711;
      case 140:
        return 49151;
      case 141:
        return 11201279;
      case 142:
        return 36285;
      case 143:
        return 8302013;
      case 144:
        return 24705;
      case 145:
        return 5666433;
      case 146:
        return 20072;
      case 147:
        return 4546408;
      case 148:
        return 15183;
      case 149:
        return 3492175;
      case 150:
        return 32767;
      case 151:
        return 11195647;
      case 152:
        return 24253;
      case 153:
        return 8297917;
      case 154:
        return 16513;
      case 155:
        return 5663617;
      case 156:
        return 13416;
      case 157:
        return 4544104;
      case 158:
        return 10063;
      case 159:
        return 3490383;
      case 160:
        return 16383;
      case 161:
        return 11190271;
      case 162:
        return 11965;
      case 163:
        return 8293821;
      case 164:
        return 8065;
      case 165:
        return 5660801;
      case 166:
        return 6504;
      case 167:
        return 4542056;
      case 168:
        return 4943;
      case 169:
        return 3488591;
      case 170:
        return 255;
      case 171:
        return 11184895;
      case 172:
        return 189;
      case 173:
        return 8289981;
      case 174:
        return 129;
      case 175:
        return 5658241;
      case 176:
        return 104;
      case 177:
        return 4539752;
      case 178:
        return 79;
      case 179:
        return 3487055;
      case 180:
        return 4129023;
      case 181:
        return 12561151;
      case 182:
        return 3014845;
      case 183:
        return 9273021;
      case 184:
        return 2031745;
      case 185:
        return 6313601;
      case 186:
        return 1638504;
      case 187:
        return 5129576;
      case 188:
        return 1245263;
      case 189:
        return 3880271;
      case 190:
        return 8323327;
      case 191:
        return 13937407;
      case 192:
        return 6160573;
      case 193:
        return 10321597;
      case 194:
        return 4194433;
      case 195:
        return 7034497;
      case 196:
        return 3407976;
      case 197:
        return 5653864;
      case 198:
        return 2555983;
      case 199:
        return 4339023;
      case 200:
        return 12517631;
      case 201:
        return 15379199;
      case 202:
        return 9240765;
      case 203:
        return 11370173;
      case 204:
        return 6291585;
      case 205:
        return 7755393;
      case 206:
        return 5111912;
      case 207:
        return 6243688;
      case 208:
        return 3866703;
      case 209:
        return 4797775;
      case 210:
        return 16711935;
      case 211:
        return 16755455;
      case 212:
        return 12386493;
      case 213:
        return 12418749;
      case 214:
        return 8454273;
      case 215:
        return 8476289;
      case 216:
        return 6815848;
      case 217:
        return 6833512;
      case 218:
        return 5177423;
      case 219:
        return 5190991;
      case 220:
        return 16711871;
      case 221:
        return 16755434;
      case 222:
        return 12386445;
      case 223:
        return 12418733;
      case 224:
        return 8454240;
      case 225:
        return 8476278;
      case 226:
        return 6815822;
      case 227:
        return 6833503;
      case 228:
        return 5177403;
      case 229:
        return 5190985;
      case 230:
        return 16711807;
      case 231:
        return 16755412;
      case 232:
        return 12386398;
      case 233:
        return 12418717;
      case 234:
        return 8454208;
      case 235:
        return 8476267;
      case 236:
        return 6815796;
      case 237:
        return 6833494;
      case 238:
        return 5177383;
      case 239:
        return 5190978;
      case 240:
        return 16711743;
      case 241:
        return 16755391;
      case 242:
        return 12386350;
      case 243:
        return 12418701;
      case 244:
        return 8454175;
      case 245:
        return 8476256;
      case 246:
        return 6815769;
      case 247:
        return 6833486;
      case 248:
        return 5177363;
      case 249:
        return 5190971;
      case 250:
        return 3355443;
      case 251:
        return 5263440;
      case 252:
        return 6908265;
      case 253:
        return 8553090;
      case 254:
        return 12500670;
      case 255:
        return 16777215;
    }
    return 16777215;
  }
}
class Ju {
  constructor() {
    this.xAxis = new R(1, 0, 0), this.yAxis = new R(0, 1, 0), this.zAxis = new R(0, 0, 1);
  }
  /**
   * Returns an index array for a line.
   * @param points {Array} vertex array to create an index.
      * @return {Array} index array
  */
  generatePointIndex(t) {
    let e = [];
    for (let s = 1; s < t.length; s++)
      e.push(s - 1), e.push(s);
    return e;
  }
  /**
   * Prepares given THREE.Line object to draw dashed lines, changing to a non indexed geometry & computing line distances.
   * @param line {THREE.Line} THREE.Line object to prepare for dashed lines.
  */
  fixMeshToDrawDashedLines(t) {
    t.geometry.index && (t.geometry = t.geometry.toNonIndexed()), t.computeLineDistances();
  }
}
class kc {
  constructor() {
  }
  parse(t) {
    const e = {};
    let s = new Kr();
    const i = Object.keys(t);
    for (let n = 0; n < i.length; n++) {
      const r = t[i[n]];
      e[i[n]] = {
        name: r.name,
        lineTypeName: r.lineTypeName,
        lineWeightEnum: r.lineWeightEnum,
        color: s.getColorByNumber(r.colorNumber),
        visible: this.isVisible(r),
        flags: this.parseFlags(r.flags)
      };
    }
    return e;
  }
  /**
   * Returns the separated flags based on the flag number.
      * @param flag {Number} A flag to indicate the type of material to be created. It can be 'shape', 'line' or 'dashed'.
      * @return {Array} frozen, locaked and dependent flags
  */
  parseFlags(t) {
    if (t === 0) return [];
    let e = [];
    return t % 2 !== 0 && e.push("frozen"), (4 <= t && t < 16 || t === 20 || t === 36 || t === 52) && e.push("locked"), 16 <= t && t < 64 && e.push("dependent"), e;
  }
  /**
   * Returns if layer is visible ot not.
      * @param layer {Layer} layer object.
      * @return {Boolean} layer visibility
  */
  isVisible(t) {
    return !(t.colorNumber < 0 || typeof t.plot < "u" && !t.plot);
  }
}
class Gt extends Ku {
  constructor(t) {
    super(), this.data = t, this._colorHelper = new Kr(), this._geometryHelper = new Ju(), this._layerHelper = new kc();
  }
  /**
      * 
      * @param entity {Entity} hides the entity according to entity's flags & general properties
      */
  _hideEntity(t) {
    if (typeof t.visible < "u" && !t.visible || typeof t.paperSpace < "u" && P.showFrozen !== t.paperSpace) return !0;
    let e = Object.prototype.hasOwnProperty.call(this.data.tables.layers, t.layer) ? this.data.tables.layers[t.layer] : null;
    return !!(e && (!e.visible || !P.showFrozen && e.flags.includes("frozen") || !P.showLocked && e.flags.includes("locked")));
  }
  //micro-optimization. Use this instead of find
  _getBlock(t, e) {
    for (let s = 0; s < t.length; s++)
      if (t[s].name === e) return t[s];
    return null;
  }
}
/**
 * @license
 * Credits to vagran (https://github.com/vagran/dxf-viewer) for the original code.
 */
const A = Object.freeze({
  TEXT: 0,
  ESCAPE: 1,
  /* Skip currently unsupported format codes till ';' */
  SKIP_FORMAT: 2,
  /* For \pxq* paragraph formatting. Not found documentation yet, so temporal naming for now. */
  PARAGRAPH1: 3,
  PARAGRAPH2: 4,
  PARAGRAPH3: 5
}), At = Object.freeze({
  TEXT: 0,
  SCOPE: 1,
  PARAGRAPH: 2,
  NON_BREAKING_SPACE: 3,
  /** "alignment" property is either "r", "c", "l", "j", "d" for right, center, left, justify
      * (seems to be the same as left), distribute (justify) alignment.
      */
  PARAGRAPH_ALIGNMENT: 4
  /* Many others are not yet implemented. */
}), Du = /* @__PURE__ */ new Set([
  "L",
  "l",
  "O",
  "o",
  "K",
  "k",
  "P",
  "X",
  "~"
]), Qu = /* @__PURE__ */ new Set([
  "f",
  "F",
  "p",
  "Q",
  "H",
  "W",
  "S",
  "A",
  "C",
  "T"
]), ju = /* @__PURE__ */ new Set([
  "\\",
  "{",
  "}"
]);
class ze {
  constructor() {
    this.entities = [];
  }
  /**
      * @param text {string} MTEXT formatted text.
      * @return {Array} this
      */
  Parse(t) {
    const e = t.length;
    let s = 0, i = A.TEXT, n = [], r = this.entities, o = 0;
    const l = this;
    function c() {
      i !== A.TEXT || s === o || (r.push({
        type: At.TEXT,
        content: t.slice(s, o)
      }), s = o);
    }
    function h(p) {
      r.push({ type: p });
    }
    function u() {
      const p = {
        type: At.SCOPE,
        content: []
      };
      r.push(p), r = p.content, n.push(p);
    }
    function d() {
      n.length !== 0 && (n.pop(), n.length === 0 ? r = l.entities : r = n[n.length - 1].content);
    }
    for (; o < e; o++) {
      const p = t.charAt(o);
      switch (i) {
        case A.TEXT:
          if (p === "{") {
            c(), u(), s = o + 1;
            continue;
          }
          if (p === "}") {
            c(), d(), s = o + 1;
            continue;
          }
          if (p === "\\") {
            c(), i = A.ESCAPE;
            continue;
          }
          continue;
        case A.ESCAPE:
          if (Du.has(p)) {
            switch (p) {
              case "P":
                h(At.PARAGRAPH);
                break;
              case "~":
                h(At.NON_BREAKING_SPACE);
                break;
            }
            i = A.TEXT, s = o + 1;
            continue;
          }
          if (Qu.has(p)) {
            switch (p) {
              case "p":
                i = A.PARAGRAPH1;
                continue;
            }
            i = A.SKIP_FORMAT;
            continue;
          }
          ju.has(p) ? s = o : s = o - 1, i = A.TEXT;
          continue;
        case A.PARAGRAPH1:
          i = p === "x" ? A.PARAGRAPH2 : A.SKIP_FORMAT;
          continue;
        case A.PARAGRAPH2:
          i = p === "q" ? A.PARAGRAPH3 : A.SKIP_FORMAT;
          continue;
        case A.PARAGRAPH3:
          r.push({ type: At.PARAGRAPH_ALIGNMENT, alignment: p }), i = A.SKIP_FORMAT;
          continue;
        case A.SKIP_FORMAT:
          p === ";" && (s = o + 1, i = A.TEXT);
          continue;
        default:
          throw new Error("Unhandled state");
      }
    }
    return c(), this;
  }
  /** @typedef MTextFormatEntity
      * @property type One of EntityType
      *
      * @return {MTextFormatEntity[]} List of format chunks. Each chunk is either a text chunk with
      * TEXT type or some format entity. Entity with type SCOPE represents format scope which has
      * nested list of entities in "content" property.
      */
  GetContent() {
    return this.entities;
  }
  /** Return only text chunks in a flattened sequence of strings. */
  *GetText() {
    function* t(e) {
      for (const s of e)
        s.type === At.TEXT ? yield s.content : s.type === At.SCOPE && (yield* t(s.content));
    }
    yield* t(this.GetContent());
  }
}
ze.EntityType = At;
var He, Ye;
const Se = class Se extends Gt {
  static get TextHeight() {
    return Ys(this, He);
  }
  static set TextHeight(t) {
    As(this, He, t || 12);
  }
  static get TextScale() {
    return Ys(this, Ye);
  }
  static set TextScale(t) {
    As(this, Ye, t || 1);
  }
  constructor(t, e) {
    super(t), this._font = e, this._textParser = new ze();
  }
  /**
   * It filters all the text, mtext and attrib entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t) {
    let e = t.entities.filter((i) => i.type === "MTEXT" || i.type === "TEXT" || i.type === "ATTRIB");
    if (e.length === 0) return null;
    let s = new at();
    s.name = "TEXTS";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = this.drawText(n);
        if (!h) continue;
        o = h.geometry, l = h.material, P.onBeforeTextDraw || this._setCache(n, h);
      }
      let c = new gt(o, l);
      c.userData = { entity: n }, s.add(c);
    }
    return s;
  }
  /**
   * Draws text, mtext and attrib entities.
   * @param entity {entity} dxf parsed text, mtext or attrib entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawText(t) {
    if (t.type === "ATTRIB")
      return Object.keys(t.mtext).length !== 0 ? this.drawText(t.mtext) : this.drawText(t.text);
    let e = this._getTextGeometry(t);
    if (!e) return null;
    this._scaleText(e, t);
    let s = this._getPosAndRotation(t);
    s.rotation && e.applyQuaternion(s.rotation), e.translate(s.pos.x, s.pos.y, s.pos.z), this._alignText(e, t, s.pos), this._translateCenter(e, t, s.pos);
    let i = this._colorHelper.getMaterial(t, "shape", this.data.tables);
    return { geometry: e, material: i };
  }
  _getTextGeometry(t) {
    let e = this._getTextStrings(t);
    if (e.length === 0) return null;
    let s = e.join("");
    if (P.onBeforeTextDraw) {
      let r = { text: s };
      P.onBeforeTextDraw(r), s = r.text;
    }
    let i = this._getTextHeight(t), n = this._font.generateShapes(s, i);
    return new Ke(n);
  }
  _getTextHeight(t) {
    let e = Se.TextHeight;
    return typeof t.nominalTextHeight < "u" ? e = t.nominalTextHeight : typeof t.textHeight < "u" && (e = t.textHeight), e *= Se.TextScale, e;
  }
  _scaleText(t, e) {
    t.computeBoundingBox();
    let s = t.boundingBox.max.x - t.boundingBox.min.x, i = t.boundingBox.max.y - t.boundingBox.min.y, n = typeof e.horizontalWidth < "u" ? e.horizontalWidth / s : 1, r = typeof e.verticalHeight < "u" ? e.verticalHeight / i : 1;
    t.scale(n, r, 1);
  }
  _getTextStrings(t) {
    if (typeof t.string > "u") return [];
    let e = (n) => {
      let r = [];
      for (let o = 0; o < n.length; o++) {
        const l = n[o];
        if ((l.type === ze.EntityType.PARAGRAPH || l.type === ze.EntityType.PARAGRAPH_ALIGNMENT) && r.push(`
`), typeof l.content == "string")
          r.push(l.content);
        else if (l.content instanceof Array) {
          let c = e(l.content);
          c && r.push(c);
        }
      }
      return r;
    }, s = new ze().Parse(t.string);
    return e(s.entities);
  }
  _getPosAndRotation(t) {
    let e = { pos: { x: 0, y: 0, z: 0 }, rotation: null }, s = t.x, i = t.y, n = t.z, r = t.rotation, o = t.xAxisX, l = t.xAxisY, c = t.xAxisZ, h = t.drawingDirection, u = "0".repeat(P.decimals), d = "0." + u, p = "1." + u, m = typeof s < "u" ? s.toFixed(P.decimals) : d, f = typeof i < "u" ? i.toFixed(P.decimals) : d, b = typeof n < "u" ? n.toFixed(P.decimals) : d, y = m === p && f === d && b === d || m === d && f === p && b === d || m === d && f === d && b === p;
    return e.pos.x = y ? o : s, e.pos.y = y ? l : i, e.pos.z = y ? c : n, r ? e.rotation = new ee().setFromAxisAngle(this._geometryHelper.zAxis, r * Math.PI / 180) : (h === 3 || y && i === 1 || !y && l === 1) && (e.rotation = new ee().setFromAxisAngle(this._geometryHelper.zAxis, Math.PI / 2)), e;
  }
  _translateCenter(t, e, s) {
    let i = e.attachmentPoint;
    t.computeBoundingBox();
    let n = t.boundingBox.max.x - t.boundingBox.min.x, r = t.boundingBox.max.y - t.boundingBox.min.y, o = t.boundingBox.max.z - t.boundingBox.min.z, l = {
      x: t.boundingBox.min.x + n / 2,
      y: t.boundingBox.min.y + r / 2
    };
    if (!i) {
      t.translate(s.x - t.boundingBox.min.x, s.y - t.boundingBox.min.y, -0.5 * o);
      return;
    }
    switch (i) {
      case 1:
        t.translate(s.x - t.boundingBox.min.x, s.y - t.boundingBox.max.y, -0.5 * o);
        break;
      case 2:
        t.translate(s.x - l.x, s.y - t.boundingBox.max.y, -0.5 * o);
        break;
      case 3:
        t.translate(s.x - t.boundingBox.max.x, s.y - t.boundingBox.max.y, -0.5 * o);
        break;
      case 4:
        t.translate(s.x - t.boundingBox.min.x, s.y - l.y, -0.5 * o);
        break;
      case 5:
        t.translate(s.x - l.x, s.y - l.y, -0.5 * o);
        break;
      case 6:
        t.translate(s.x - t.boundingBox.max.x, s.y - l.y, -0.5 * o);
        break;
      case 7:
        t.translate(s.x - t.boundingBox.min.x, s.y - t.boundingBox.min.y, -0.5 * o);
        break;
      case 8:
        t.translate(s.x - l.x, t.boundingBox.min.y, -0.5 * o);
        break;
      case 9:
        t.translate(s.x - t.boundingBox.max.x, t.boundingBox.min.y, -0.5 * o);
        break;
    }
  }
  _alignText(t, e, s) {
    t.computeBoundingBox();
    let i = t.boundingBox.max.x - t.boundingBox.min.x, n = t.boundingBox.max.y - t.boundingBox.min.y, r = {
      x: t.boundingBox.min.x + i / 2,
      y: t.boundingBox.min.y + n / 2
    }, o = 0, l = n;
    switch (e.hAlign) {
      case 0:
        o = s.x - t.boundingBox.min.x;
        break;
      case 1:
        o = s.x - r.x;
        break;
      case 2:
        o = s.x - t.boundingBox.max.x;
        break;
    }
    switch (e.vAlign) {
      case 0:
        l = s.y - r.y;
        break;
      case 1:
        l = n / 2;
        break;
      case 2:
        l = s.y - r.y;
        break;
      case 3:
        l = s.y - t.boundingBox.max.y;
        break;
    }
    t.translate(o, l, 0);
  }
  _replaceSpecialChars(t) {
    return t.replaceAll("\\P", `
`).replaceAll("\\X", `
`).replaceAll("%%d", "°").replaceAll("%%p", "±").replaceAll("%%c", "∅").replaceAll("%%%", "%");
  }
};
He = new WeakMap(), Ye = new WeakMap(), Ps(Se, He, 12), Ps(Se, Ye, 1);
let zt = Se;
var Zs = typeof globalThis < "u" ? globalThis : typeof window < "u" ? window : typeof global < "u" ? global : typeof self < "u" ? self : {};
function Jr(a) {
  return a && a.__esModule && Object.prototype.hasOwnProperty.call(a, "default") ? a.default : a;
}
var Ri = {}, Si = {}, Zi = {}, Da;
function Dr() {
  return Da || (Da = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    function t(o) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? t = function(c) {
        return typeof c;
      } : t = function(c) {
        return c && typeof Symbol == "function" && c.constructor === Symbol && c !== Symbol.prototype ? "symbol" : typeof c;
      }, t(o);
    }
    function e(o, l) {
      if (!(o instanceof l))
        throw new TypeError("Cannot call a class as a function");
    }
    function s(o, l) {
      for (var c = 0; c < l.length; c++) {
        var h = l[c];
        h.enumerable = h.enumerable || !1, h.configurable = !0, "value" in h && (h.writable = !0), Object.defineProperty(o, h.key, h);
      }
    }
    function i(o, l, c) {
      return l && s(o.prototype, l), o;
    }
    var n = /* @__PURE__ */ function() {
      function o(l, c) {
        e(this, o), t(l) === "object" ? (this.x = l.x, this.y = l.y) : (this.x = l, this.y = c);
      }
      return i(o, [{
        key: "equals",
        value: function(c) {
          return this.x === c.x && this.y === c.y;
        }
      }, {
        key: "length",
        value: function() {
          return Math.sqrt(this.dot(this));
        }
      }, {
        key: "neg",
        value: function() {
          return new o(-this.x, -this.y);
        }
      }, {
        key: "add",
        value: function(c) {
          return new o(this.x + c.x, this.y + c.y);
        }
      }, {
        key: "sub",
        value: function(c) {
          return new o(this.x - c.x, this.y - c.y);
        }
      }, {
        key: "multiply",
        value: function(c) {
          return new o(this.x * c, this.y * c);
        }
      }, {
        key: "norm",
        value: function() {
          return this.multiply(1 / this.length());
        }
      }, {
        key: "dot",
        value: function(c) {
          return this.x * c.x + this.y * c.y;
        }
      }]), o;
    }(), r = n;
    a.default = r;
  }(Zi)), Zi;
}
var Gi = {}, Qa;
function Ws() {
  return Qa || (Qa = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    function t(o) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? t = function(c) {
        return typeof c;
      } : t = function(c) {
        return c && typeof Symbol == "function" && c.constructor === Symbol && c !== Symbol.prototype ? "symbol" : typeof c;
      }, t(o);
    }
    function e(o, l) {
      if (!(o instanceof l))
        throw new TypeError("Cannot call a class as a function");
    }
    function s(o, l) {
      for (var c = 0; c < l.length; c++) {
        var h = l[c];
        h.enumerable = h.enumerable || !1, h.configurable = !0, "value" in h && (h.writable = !0), Object.defineProperty(o, h.key, h);
      }
    }
    function i(o, l, c) {
      return l && s(o.prototype, l), o;
    }
    var n = /* @__PURE__ */ function() {
      function o(l, c, h) {
        e(this, o), t(l) === "object" ? (this.x = l.x, this.y = l.y, this.z = l.z) : l === void 0 ? (this.x = 0, this.y = 0, this.z = 0) : (this.x = l, this.y = c, this.z = h);
      }
      return i(o, [{
        key: "equals",
        value: function(c, h) {
          return h === void 0 && (h = 0), Math.abs(this.x - c.x) <= h && Math.abs(this.y - c.y) <= h && Math.abs(this.z - c.z) <= h;
        }
      }, {
        key: "length",
        value: function() {
          return Math.sqrt(this.dot(this));
        }
      }, {
        key: "neg",
        value: function() {
          return new o(-this.x, -this.y, -this.z);
        }
      }, {
        key: "add",
        value: function(c) {
          return new o(this.x + c.x, this.y + c.y, this.z + c.z);
        }
      }, {
        key: "sub",
        value: function(c) {
          return new o(this.x - c.x, this.y - c.y, this.z - c.z);
        }
      }, {
        key: "multiply",
        value: function(c) {
          return new o(this.x * c, this.y * c, this.z * c);
        }
      }, {
        key: "norm",
        value: function() {
          return this.multiply(1 / this.length());
        }
      }, {
        key: "dot",
        value: function(c) {
          return this.x * c.x + this.y * c.y + this.z * c.z;
        }
      }, {
        key: "cross",
        value: function(c) {
          return new o(this.y * c.z - this.z * c.y, this.z * c.x - this.x * c.z, this.x * c.y - this.y * c.x);
        }
      }]), o;
    }(), r = n;
    a.default = r;
  }(Gi)), Gi;
}
var Vi = {}, ja;
function Ou() {
  return ja || (ja = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(Dr());
    function e(c) {
      return c && c.__esModule ? c : { default: c };
    }
    function s(c) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? s = function(u) {
        return typeof u;
      } : s = function(u) {
        return u && typeof Symbol == "function" && u.constructor === Symbol && u !== Symbol.prototype ? "symbol" : typeof u;
      }, s(c);
    }
    function i(c, h) {
      if (!(c instanceof h))
        throw new TypeError("Cannot call a class as a function");
    }
    function n(c, h) {
      for (var u = 0; u < h.length; u++) {
        var d = h[u];
        d.enumerable = d.enumerable || !1, d.configurable = !0, "value" in d && (d.writable = !0), Object.defineProperty(c, d.key, d);
      }
    }
    function r(c, h, u) {
      return h && n(c.prototype, h), c;
    }
    var o = /* @__PURE__ */ function() {
      function c(h, u) {
        if (i(this, c), s(h) === "object" && s(u) === "object" && h.x !== void 0 && h.y !== void 0 && u.x !== void 0 && u.y !== void 0)
          this.min = new t.default(h), this.max = new t.default(u), this.valid = !0;
        else if (h === void 0 && u === void 0)
          this.min = new t.default(1 / 0, 1 / 0), this.max = new t.default(-1 / 0, -1 / 0), this.valid = !1;
        else
          throw Error("Illegal construction - must use { x, y } objects");
      }
      return r(c, [{
        key: "equals",
        value: function(u) {
          if (!this.valid)
            throw Error("Box2 is invalid");
          return this.min.equals(u.min) && this.max.equals(u.max);
        }
      }, {
        key: "expandByPoint",
        value: function(u) {
          return this.min = new t.default(Math.min(this.min.x, u.x), Math.min(this.min.y, u.y)), this.max = new t.default(Math.max(this.max.x, u.x), Math.max(this.max.y, u.y)), this.valid = !0, this;
        }
      }, {
        key: "expandByPoints",
        value: function(u) {
          var d = this;
          return u.forEach(function(p) {
            d.expandByPoint(p);
          }, this), this;
        }
      }, {
        key: "isPointInside",
        value: function(u) {
          return u.x >= this.min.x && u.y >= this.min.y && u.x <= this.max.x && u.y <= this.max.y;
        }
      }, {
        key: "width",
        get: function() {
          if (!this.valid)
            throw Error("Box2 is invalid");
          return this.max.x - this.min.x;
        }
      }, {
        key: "height",
        get: function() {
          if (!this.valid)
            throw Error("Box2 is invalid");
          return this.max.y - this.min.y;
        }
      }]), c;
    }();
    o.fromPoints = function(c) {
      return new o().expandByPoints(c);
    };
    var l = o;
    a.default = l;
  }(Vi)), Vi;
}
var Mi = {}, Oa;
function qu() {
  return Oa || (Oa = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(Ws());
    function e(c) {
      return c && c.__esModule ? c : { default: c };
    }
    function s(c) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? s = function(u) {
        return typeof u;
      } : s = function(u) {
        return u && typeof Symbol == "function" && u.constructor === Symbol && u !== Symbol.prototype ? "symbol" : typeof u;
      }, s(c);
    }
    function i(c, h) {
      if (!(c instanceof h))
        throw new TypeError("Cannot call a class as a function");
    }
    function n(c, h) {
      for (var u = 0; u < h.length; u++) {
        var d = h[u];
        d.enumerable = d.enumerable || !1, d.configurable = !0, "value" in d && (d.writable = !0), Object.defineProperty(c, d.key, d);
      }
    }
    function r(c, h, u) {
      return h && n(c.prototype, h), c;
    }
    var o = /* @__PURE__ */ function() {
      function c(h, u) {
        if (i(this, c), s(h) === "object" && s(u) === "object" && h.x !== void 0 && h.y !== void 0 && h.z !== void 0 && u.x !== void 0 && u.y !== void 0 && u.z !== void 0)
          this.min = new t.default(h), this.max = new t.default(u), this.valid = !0;
        else if (h === void 0 && u === void 0)
          this.min = new t.default(1 / 0, 1 / 0, 1 / 0), this.max = new t.default(-1 / 0, -1 / 0, -1 / 0), this.valid = !1;
        else
          throw Error("Illegal construction - must use { x, y, z } objects");
      }
      return r(c, [{
        key: "equals",
        value: function(u) {
          if (!this.valid)
            throw Error("Box3 is invalid");
          return this.min.equals(u.min) && this.max.equals(u.max);
        }
      }, {
        key: "expandByPoint",
        value: function(u) {
          return this.min = new t.default(Math.min(this.min.x, u.x), Math.min(this.min.y, u.y), Math.min(this.min.z, u.z)), this.max = new t.default(Math.max(this.max.x, u.x), Math.max(this.max.y, u.y), Math.max(this.max.z, u.z)), this.valid = !0, this;
        }
      }, {
        key: "expandByPoints",
        value: function(u) {
          var d = this;
          return u.forEach(function(p) {
            d.expandByPoint(p);
          }, this), this;
        }
      }, {
        key: "isPointInside",
        value: function(u) {
          return u.x >= this.min.x && u.y >= this.min.y && u.z >= this.min.z && u.x <= this.max.x && u.y <= this.max.y && u.z <= this.max.z;
        }
      }, {
        key: "width",
        get: function() {
          if (!this.valid)
            throw Error("Box3 is invalid");
          return this.max.x - this.min.x;
        }
      }, {
        key: "depth",
        get: function() {
          if (!this.valid)
            throw Error("Box3 is invalid");
          return this.max.y - this.min.y;
        }
      }, {
        key: "height",
        get: function() {
          if (!this.valid)
            throw Error("Box3 is invalid");
          return this.max.z - this.min.z;
        }
      }]), c;
    }();
    o.fromPoints = function(c) {
      return new o().expandByPoints(c);
    };
    var l = o;
    a.default = l;
  }(Mi)), Mi;
}
var Li = {}, qa;
function $u() {
  return qa || (qa = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    function t(r, o) {
      if (!(r instanceof o))
        throw new TypeError("Cannot call a class as a function");
    }
    function e(r, o) {
      for (var l = 0; l < o.length; l++) {
        var c = o[l];
        c.enumerable = c.enumerable || !1, c.configurable = !0, "value" in c && (c.writable = !0), Object.defineProperty(r, c.key, c);
      }
    }
    function s(r, o, l) {
      return o && e(r.prototype, o), r;
    }
    var i = /* @__PURE__ */ function() {
      function r(o, l, c, h) {
        t(this, r), this.a = o, this.b = l, this.c = c, this.d = h;
      }
      return s(r, [{
        key: "distanceToPoint",
        value: function(l) {
          var c = (this.a * l.x + this.b * l.y + this.c * l.z + this.d) / Math.sqrt(this.a * this.a + this.b * this.b + this.c * this.c);
          return c;
        }
      }, {
        key: "equals",
        value: function(l) {
          return this.a === l.a && this.b === l.b && this.c === l.c && this.d === l.d;
        }
      }, {
        key: "coPlanar",
        value: function(l) {
          var c = this.a === l.a && this.b === l.b && this.c === l.c && this.d === l.d, h = this.a === -l.a && this.b === -l.b && this.c === -l.c && this.d === -l.d;
          return c || h;
        }
      }]), r;
    }();
    i.fromPointAndNormal = function(r, o) {
      var l = o.x, c = o.y, h = o.z, u = -(r.x * l + r.y * c + r.z * h);
      return new i(o.x, o.y, o.z, u);
    }, i.fromPoints = function(r) {
      for (var o, l = 0, c = r.length; l < c; ++l) {
        var h = r[(l + 1) % c].sub(r[l]), u = r[(l + 2) % c].sub(r[(l + 1) % c]), d = h.cross(u);
        if (!(isNaN(d.length()) || d.length() === 0))
          if (!o)
            o = d.norm();
          else {
            var p = d.norm().equals(o, 1e-6), m = d.neg().norm().equals(o, 1e-6);
            if (!(p || m))
              throw Error("points not on a plane");
          }
      }
      if (!o)
        throw Error("points not on a plane");
      return i.fromPointAndNormal(r[0], o.norm());
    };
    var n = i;
    a.default = n;
  }(Li)), Li;
}
var wi = {}, $a;
function td() {
  return $a || ($a = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(Ws());
    function e(l) {
      return l && l.__esModule ? l : { default: l };
    }
    function s(l, c) {
      if (!(l instanceof c))
        throw new TypeError("Cannot call a class as a function");
    }
    function i(l, c) {
      for (var h = 0; h < c.length; h++) {
        var u = c[h];
        u.enumerable = u.enumerable || !1, u.configurable = !0, "value" in u && (u.writable = !0), Object.defineProperty(l, u.key, u);
      }
    }
    function n(l, c, h) {
      return c && i(l.prototype, c), l;
    }
    var r = /* @__PURE__ */ function() {
      function l(c, h, u, d) {
        s(this, l), this.x = c, this.y = h, this.z = u, this.w = d;
      }
      return n(l, [{
        key: "applyToVec3",
        value: function(h) {
          var u = h.x, d = h.y, p = h.z, m = this.x, f = this.y, b = this.z, y = this.w, x = y * u + f * p - b * d, g = y * d + b * u - m * p, S = y * p + m * d - f * u, Z = -m * u - f * d - b * p;
          return new t.default(x * y + Z * -m + g * -b - S * -f, g * y + Z * -f + S * -m - x * -b, S * y + Z * -b + x * -f - g * -m);
        }
      }]), l;
    }();
    r.fromAxisAngle = function(l, c) {
      var h = l.norm(), u = c / 2, d = Math.sin(u);
      return new r(h.x * d, h.y * d, h.z * d, Math.cos(u));
    };
    var o = r;
    a.default = o;
  }(wi)), wi;
}
var vi = {}, to;
function ed() {
  return to || (to = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(Dr());
    function e(p) {
      return p && p.__esModule ? p : { default: p };
    }
    function s(p) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? s = function(f) {
        return typeof f;
      } : s = function(f) {
        return f && typeof Symbol == "function" && f.constructor === Symbol && f !== Symbol.prototype ? "symbol" : typeof f;
      }, s(p);
    }
    function i(p, m) {
      if (!(p instanceof m))
        throw new TypeError("Cannot call a class as a function");
    }
    function n(p, m) {
      for (var f = 0; f < m.length; f++) {
        var b = m[f];
        b.enumerable = b.enumerable || !1, b.configurable = !0, "value" in b && (b.writable = !0), Object.defineProperty(p, b.key, b);
      }
    }
    function r(p, m, f) {
      return m && n(p.prototype, m), p;
    }
    var o = function(m, f, b) {
      var y = m.x, x = m.y, g = f.x, S = f.y, Z = b.x, V = b.y, G = (V - x) * (g - y), M = (S - x) * (Z - y);
      return G > M + Number.EPSILON ? 1 : G + Number.EPSILON < M ? -1 : 0;
    }, l = function(m, f) {
      var b = m.a, y = m.b, x = f.a, g = f.b;
      return o(b, x, g) !== o(y, x, g) && o(b, y, x) !== o(b, y, g);
    }, c = function(m, f) {
      var b = m.a.x, y = m.b.x, x = m.a.y, g = m.b.y, S = f.a.x, Z = f.b.x, V = f.a.y, G = f.b.y, M = b - y, L = S - Z, k = x - g, w = V - G, E = M * w - k * L, F = ((b * g - x * y) * L - M * (S * G - V * Z)) / E, X = ((b * g - x * y) * w - k * (S * G - V * Z)) / E;
      return isNaN(F) || isNaN(X) ? null : new t.default(F, X);
    }, h = function(m, f) {
      return Math.sqrt(Math.pow(m.x - f.x, 2) + Math.pow(m.y - f.y, 2));
    }, u = /* @__PURE__ */ function() {
      function p(m, f) {
        if (i(this, p), s(m) !== "object" || m.x === void 0 || m.y === void 0)
          throw Error("expected first argument to have x and y properties");
        if (s(f) !== "object" || f.x === void 0 || f.y === void 0)
          throw Error("expected second argument to have x and y properties");
        this.a = new t.default(m), this.b = new t.default(f);
      }
      return r(p, [{
        key: "length",
        value: function() {
          return this.a.sub(this.b).length();
        }
      }, {
        key: "intersects",
        value: function(f) {
          if (!(f instanceof p))
            throw new Error("expected argument to be an instance of vecks.Line2");
          return l(this, f);
        }
      }, {
        key: "getIntersection",
        value: function(f) {
          return this.intersects(f) ? c(this, f) : null;
        }
      }, {
        key: "containsPoint",
        value: function(f) {
          var b = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : 1e-12;
          return Math.abs(h(this.a, this.b) - h(f, this.a) - h(f, this.b)) < b;
        }
      }]), p;
    }(), d = u;
    a.default = d;
  }(vi)), vi;
}
var Fi = {}, eo;
function sd() {
  return eo || (eo = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(Ws());
    function e(h) {
      return h && h.__esModule ? h : { default: h };
    }
    function s(h) {
      "@babel/helpers - typeof";
      return typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? s = function(d) {
        return typeof d;
      } : s = function(d) {
        return d && typeof Symbol == "function" && d.constructor === Symbol && d !== Symbol.prototype ? "symbol" : typeof d;
      }, s(h);
    }
    function i(h, u) {
      if (!(h instanceof u))
        throw new TypeError("Cannot call a class as a function");
    }
    function n(h, u) {
      for (var d = 0; d < u.length; d++) {
        var p = u[d];
        p.enumerable = p.enumerable || !1, p.configurable = !0, "value" in p && (p.writable = !0), Object.defineProperty(h, p.key, p);
      }
    }
    function r(h, u, d) {
      return u && n(h.prototype, u), h;
    }
    var o = function(u, d) {
      return Math.sqrt(Math.pow(u.x - d.x, 2) + Math.pow(u.y - d.y, 2) + Math.pow(u.z - d.z, 2));
    }, l = /* @__PURE__ */ function() {
      function h(u, d) {
        if (i(this, h), s(u) !== "object" || u.x === void 0 || u.y === void 0 || u.z === void 0)
          throw Error("expected first argument to have x, y and z properties");
        if (s(d) !== "object" || d.x === void 0 || d.y === void 0 || d.y === void 0)
          throw Error("expected second argument to have x, y and z properties");
        this.a = new t.default(u), this.b = new t.default(d);
      }
      return r(h, [{
        key: "length",
        value: function() {
          return this.a.sub(this.b).length();
        }
      }, {
        key: "containsPoint",
        value: function(d) {
          var p = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : 1e-12;
          return Math.abs(o(this.a, this.b) - o(d, this.a) - o(d, this.b)) < p;
        }
      }]), h;
    }(), c = l;
    a.default = c;
  }(Fi)), Fi;
}
var so;
function Tc() {
  return so || (so = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), Object.defineProperty(a, "V2", {
      enumerable: !0,
      get: function() {
        return t.default;
      }
    }), Object.defineProperty(a, "V3", {
      enumerable: !0,
      get: function() {
        return e.default;
      }
    }), Object.defineProperty(a, "Box2", {
      enumerable: !0,
      get: function() {
        return s.default;
      }
    }), Object.defineProperty(a, "Box3", {
      enumerable: !0,
      get: function() {
        return i.default;
      }
    }), Object.defineProperty(a, "Plane3", {
      enumerable: !0,
      get: function() {
        return n.default;
      }
    }), Object.defineProperty(a, "Quaternion", {
      enumerable: !0,
      get: function() {
        return r.default;
      }
    }), Object.defineProperty(a, "Line2", {
      enumerable: !0,
      get: function() {
        return o.default;
      }
    }), Object.defineProperty(a, "Line3", {
      enumerable: !0,
      get: function() {
        return l.default;
      }
    });
    var t = c(Dr()), e = c(Ws()), s = c(Ou()), i = c(qu()), n = c($u()), r = c(td()), o = c(ed()), l = c(sd());
    function c(h) {
      return h && h.__esModule ? h : { default: h };
    }
  }(Si)), Si;
}
var io;
function id() {
  return io || (io = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = Tc();
    a.default = function(s, i, n, r) {
      r || (r = 5);
      var o, l, c;
      n < 0 ? (o = Math.atan(-n) * 4, l = new t.V2(s[0], s[1]), c = new t.V2(i[0], i[1])) : (o = Math.atan(n) * 4, l = new t.V2(i[0], i[1]), c = new t.V2(s[0], s[1]));
      var h = c.sub(l), u = h.length(), d = l.add(h.multiply(0.5)), p = Math.abs(u / 2 / Math.tan(o / 2)), m = h.norm(), f;
      if (o < Math.PI) {
        var b = new t.V2(m.x * Math.cos(Math.PI / 2) - m.y * Math.sin(Math.PI / 2), m.y * Math.cos(Math.PI / 2) + m.x * Math.sin(Math.PI / 2));
        f = d.add(b.multiply(-p));
      } else {
        var y = new t.V2(m.x * Math.cos(Math.PI / 2) - m.y * Math.sin(Math.PI / 2), m.y * Math.cos(Math.PI / 2) + m.x * Math.sin(Math.PI / 2));
        f = d.add(y.multiply(p));
      }
      var x = Math.atan2(c.y - f.y, c.x - f.x) / Math.PI * 180, g = Math.atan2(l.y - f.y, l.x - f.x) / Math.PI * 180;
      g < x && (g += 360);
      for (var S = c.sub(f).length(), Z = Math.floor(x / r) * r + r, V = Math.ceil(g / r) * r - r, G = [], M = Z; M <= V; M += r)
        G.push(f.add(new t.V2(Math.cos(M / 180 * Math.PI) * S, Math.sin(M / 180 * Math.PI) * S)));
      return n < 0 && G.reverse(), G.map(function(L) {
        return [L.x, L.y];
      });
    };
  }(Ri)), Ri;
}
var nd = id();
const rd = /* @__PURE__ */ Jr(nd);
class Is extends Gt {
  constructor(t) {
    super(t);
  }
  /**
   * It filters all the line, polyline and lwpolylin entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t) {
    let e = new at();
    e.name = "LINES";
    let s = t.entities.filter((i) => i.type === "LINE" || i.type === "POLYLINE" || i.type === "LWPOLYLINE");
    if (s.length === 0) return null;
    for (let i = 0; i < s.length; i++) {
      let n = s[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = n.type === "LINE" ? this.drawLine(n) : this.drawPolyLine(n);
        o = h.geometry, l = h.material, this._setCache(n, h);
      }
      let c = new Xt(o, l);
      l.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(c), c.userData = { entity: n }, e.add(c);
    }
    return e;
  }
  /**
   * Draws a line entity.
   * @param entity {entity} dxf parsed line entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawLine(t, e = 1) {
    let s = "line";
    if (t.lineTypeName) {
      let r = this.data.tables.ltypes[t.lineTypeName];
      r && r.pattern.length > 0 && (s = "dashed");
    }
    let i = this._colorHelper.getMaterial(t, s, this.data.tables), n = new rt().setFromPoints([
      { x: e * t.start.x, y: t.start.y, z: t.start.z },
      { x: e * t.end.x, y: t.end.y, z: t.end.z }
    ]);
    return n.setIndex(new q(new Uint16Array([0, 1]), 1)), { geometry: n, material: i };
  }
  /**
   * Draws a polyline or lwpolyline entity.
   * @param entity {entity} dxf parsed polyline or lwpolyline entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawPolyLine(t) {
    let e = "line";
    if (t.lineTypeName) {
      let r = this.data.tables.ltypes[t.lineTypeName];
      r && r.pattern.length > 0 && (e = "dashed");
    }
    let s = this._colorHelper.getMaterial(t, e, this.data.tables), i = this._getPolyLinePoints(t.vertices, t.closed), n = new rt().setFromPoints(i);
    return n.setIndex(new q(new Uint16Array(this._geometryHelper.generatePointIndex(i)), 1)), { geometry: n, material: s };
  }
  _getPolyLinePoints(t, e, s = 1) {
    let i = [];
    const n = new R(), r = new R();
    for (let o = 0, l = t.length; o < l - 1; ++o) {
      let c = t[o], h = t[o + 1], u = t[o].bulge;
      if (n.set(c.x, c.y, c.z), r.set(h.x, h.y, h.z), i.push({ x: c.x, y: c.y, z: c.z }), u) {
        let d = rd([c.x, c.y, c.z], [h.x, h.y, h.z], u);
        for (let p = 0, m = d.length; p < m; ++p) {
          const f = d[p];
          i.push({ x: s * f[0], y: f[1], z: f.length > 2 ? f[2] : 0 });
        }
      }
      o === l - 2 && i.push({ x: h.x, y: h.y, z: h.z });
    }
    return e && i.push(i[0]), i;
  }
}
class Qr extends Gt {
  constructor(t) {
    super(t);
  }
  /**
   * It filters all the solid entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t) {
    let e = t.entities.filter((i) => i.type === "SOLID");
    if (e.length === 0) return null;
    let s = new at();
    s.name = "SOLIDS";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = this.drawSolid(n);
        o = h.geometry, l = h.material, this._setCache(n, h);
      }
      let c = new gt(o, l);
      c.userData = { entity: n }, s.add(c);
    }
    return s;
  }
  /**
   * Draws a solid entity.
   * @param entity {entity} dxf parsed solid entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawSolid(t) {
    let e = this._colorHelper.getMaterial(t, "shape", this.data.tables), s = t.corners.map((r) => new R(r.x, r.y, r.z));
    s.splice(0, 0, s.pop());
    const i = new te(s);
    let n = new Ke(i);
    return this._extrusionTransform(t, n), { geometry: n, material: e };
  }
  _extrusionTransform(t, e) {
    t.extrusionZ < 0 && e.scale(-1, 1, 1);
  }
}
class Xc extends Gt {
  constructor(t) {
    super(t);
  }
  /**
   * It filters all the circle, arc and ellipse entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t) {
    let e = t.entities.filter((i) => i.type === "ARC" || i.type === "CIRCLE" || i.type === "ELLIPSE");
    if (e.length === 0) return null;
    let s = new at();
    s.name = "CIRCLES";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = n.type === "ELLIPSE" ? this.drawEllipse(n) : this.drawCircle(n);
        o = h.geometry, l = h.material, this._setCache(n, h);
      }
      let c = new Xt(o, l);
      l.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(c), c.userData = { entity: n }, s.add(c);
    }
    return s;
  }
  /**
   * Draws a circle or arc entity.
   * @param entity {entity} dxf parsed circle or arc entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawCircle(t) {
    let e = "line";
    if (t.lineTypeName) {
      let h = this.data.tables.ltypes[t.lineTypeName];
      h && h.pattern.length > 0 && (e = "dashed");
    }
    let s = this._colorHelper.getMaterial(t, e, this.data.tables), i, n;
    if (t.type === "CIRCLE")
      i = t.startAngle || 0, n = i + 2 * Math.PI;
    else if (i = t.startAngle, n = t.endAngle, t.extrusionZ < 0) {
      let h = this._rotateXY(t.startAngle, t.endAngle);
      i = h[1], n = h[0];
    }
    let o = new Hr(
      0,
      0,
      t.r,
      i,
      n
    ).getPoints(32), l = new rt().setFromPoints(o);
    l.setIndex(new q(new Uint16Array(this._geometryHelper.generatePointIndex(o)), 1));
    let c = {
      x: t.center ? t.center.x : t.x,
      y: t.center ? t.center.y : t.y,
      z: t.center ? t.center.z : t.z
    };
    return l.translate(t.extrusionZ < 0 ? -c.x : c.x, c.y, c.z), { geometry: l, material: s };
  }
  /**
   * Draws an ellipse entity.
   * @param entity {entity} dxf parsed ellipse entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawEllipse(t) {
    let e = "line";
    if (t.lineTypeName) {
      let u = this.data.tables.ltypes[t.lineTypeName];
      u && u.pattern.length > 0 && (e = "dashed");
    }
    let s = this._colorHelper.getMaterial(t, e, this.data.tables), i = Math.sqrt(Math.pow(t.majorX, 2) + Math.pow(t.majorY, 2)), n = i * t.axisRatio, r = Math.atan2(t.majorY, t.extrusionZ < 0 ? -t.majorX : t.majorX), o = {
      x: t.center ? t.center.x : t.x,
      y: t.center ? t.center.y : t.y,
      z: t.center ? t.center.z : t.z
    }, c = new Ae(
      o.x,
      o.y,
      i,
      n,
      t.startAngle,
      t.endAngle,
      !1,
      // Always counterclockwise
      r
    ).getPoints(32), h = new rt().setFromPoints(c);
    return h.setIndex(new q(new Uint16Array(this._geometryHelper.generatePointIndex(c)), 1)), this._extrusionTransform(t, h, o), { geometry: h, material: s };
  }
  _extrusionTransform(t, e, s) {
    t.extrusionZ < 0 && (e.translate(-s.x, -s.y, -s.z), e.scale(-1, 1, 1), e.translate(s.x, s.y, s.z));
  }
  _rotateXY(t, e) {
    const s = this._geometryHelper.xAxis.clone().applyAxisAngle(this._geometryHelper.zAxis, t), i = this._geometryHelper.xAxis.clone().applyAxisAngle(this._geometryHelper.zAxis, e);
    return s.applyAxisAngle(this._geometryHelper.yAxis, Math.PI), i.applyAxisAngle(this._geometryHelper.yAxis, Math.PI), [Math.atan2(s.y, s.x), Math.atan2(i.y, i.x)];
  }
}
var ki = {}, Ti = {}, no;
function ad() {
  return no || (no = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0, a.default = function(e, s) {
      return typeof s > "u" || +s == 0 ? Math.round(e) : (e = +e, s = +s, isNaN(e) || !(typeof s == "number" && s % 1 === 0) ? NaN : (e = e.toString().split("e"), e = Math.round(+(e[0] + "e" + (e[1] ? +e[1] - s : -s))), e = e.toString().split("e"), +(e[0] + "e" + (e[1] ? +e[1] + s : s))));
    };
  }(Ti)), Ti;
}
var ro;
function od() {
  return ro || (ro = 1, function(a) {
    Object.defineProperty(a, "__esModule", {
      value: !0
    }), a.default = void 0;
    var t = e(ad());
    function e(s) {
      return s && s.__esModule ? s : { default: s };
    }
    a.default = function(i, n, r, o, l) {
      var c = r.length, h = r[0].length;
      if (i < 0 || i > 1)
        throw new Error("t out of bounds [0,1]: " + i);
      if (n < 1) throw new Error("degree must be at least 1 (linear)");
      if (n > c - 1) throw new Error("degree must be less than or equal to point count - 1");
      if (!l) {
        l = [];
        for (var u = 0; u < c; u++)
          l[u] = 1;
      }
      if (o) {
        if (o.length !== c + n + 1) throw new Error("bad knot vector length");
      } else {
        o = [];
        for (var d = 0; d < c + n + 1; d++)
          o[d] = d;
      }
      var p = [n, o.length - 1 - n], m = o[p[0]], f = o[p[1]];
      i = i * (f - m) + m, i = Math.max(i, m), i = Math.min(i, f);
      var b;
      for (b = p[0]; b < p[1] && !(i >= o[b] && i <= o[b + 1]); b++)
        ;
      for (var y = [], x = 0; x < c; x++) {
        y[x] = [];
        for (var g = 0; g < h; g++)
          y[x][g] = r[x][g] * l[x];
        y[x][h] = l[x];
      }
      for (var S, Z = 1; Z <= n + 1; Z++)
        for (var V = b; V > b - n - 1 + Z; V--) {
          S = (i - o[V]) / (o[V + n + 1 - Z] - o[V]);
          for (var G = 0; G < h + 1; G++)
            y[V][G] = (1 - S) * y[V - 1][G] + S * y[V][G];
        }
      for (var M = [], L = 0; L < h; L++)
        M[L] = (0, t.default)(y[b][L] / y[b][h], -9);
      return M;
    };
  }(ki)), ki;
}
var ld = od();
const cd = /* @__PURE__ */ Jr(ld);
class jr extends Gt {
  constructor(t) {
    super(t);
  }
  /**
   * It filters all the spline entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t) {
    let e = t.entities.filter((i) => i.type === "SPLINE");
    if (e.length === 0) return null;
    let s = new at();
    s.name = "SPLINES";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = this.drawSpline(n);
        o = h.geometry, l = h.material, this._setCache(n, h);
      }
      let c = new Xt(o, l);
      l.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(c), c.userData = { entity: n }, s.add(c);
    }
    return s;
  }
  /**
   * Draws a spline entity.
   * @param entity {entity} dxf parsed spline entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawSpline(t) {
    let e = "line";
    if (t.lineTypeName) {
      let r = this.data.tables.ltypes[t.lineTypeName];
      r && r.pattern.length > 0 && (e = "dashed");
    }
    let s = this._colorHelper.getMaterial(t, e, this.data.tables), i = this.getBSplinePolyline(t.controlPoints, t.degree, t.knots, t.weights), n = new rt().setFromPoints(i);
    return n.setIndex(new q(new Uint16Array(this._geometryHelper.generatePointIndex(i)), 1)), { geometry: n, material: s };
  }
  getBSplinePolyline(t, e, s, i = null, n = 25) {
    const r = [], o = t.map(function(h) {
      return [h.x, h.y];
    }), l = [s[e]], c = [s[e], s[s.length - 1 - e]];
    for (let h = e + 1; h < s.length - e; ++h)
      l[l.length - 1] !== s[h] && l.push(s[h]);
    for (let h = 1; h < l.length; ++h) {
      const u = l[h - 1], d = l[h];
      for (let p = 0; p <= n; ++p) {
        let f = (p / n * (d - u) + u - c[0]) / (c[1] - c[0]);
        f = Math.max(f, 0), f = Math.min(f, 1);
        const b = cd(f, e, o, s, i);
        isNaN(b[0]) || isNaN(b[1]) || r.push({ x: b[0], y: b[1], z: 0 });
      }
    }
    return r;
  }
}
const $t = {
  ODD_PARITY: 0,
  OUTERMOST: 1,
  THROUGH_ENTIRE_AREA: 2
}, Gs = 1e-9;
function ao(a, t) {
  return a.x * t.x + a.y * t.y;
}
function oo(a, t) {
  return { x: a.x - t.x, y: a.y - t.y };
}
function hd(a, t) {
  return { x: a.x + t.x, y: a.y + t.y };
}
function ud(a, t) {
  return { x: a.x * t, y: a.y * t };
}
function dd(a) {
  return Math.hypot(a.x, a.y);
}
class pd {
  constructor(t, e = $t.ODD_PARITY) {
    this.loops = (t || []).map((s) => {
      const i = s.slice();
      if (i.length >= 2) {
        const n = i[0], r = i[i.length - 1];
        (Math.abs(n.x - r.x) > Gs || Math.abs(n.y - r.y) > Gs) && i.push({ x: n.x, y: n.y });
      }
      return i;
    }), this.style = e;
  }
  // Even-odd PIP test for a single loop
  _pointInLoop(t, e) {
    let s = !1;
    for (let i = 0, n = e.length - 1; i < e.length; n = i++) {
      const r = e[i].x, o = e[i].y, l = e[n].x, c = e[n].y;
      o > t.y != c > t.y && t.x < (l - r) * (t.y - o) / (c - o + 1e-30) + r && (s = !s);
    }
    return s;
  }
  // Return how many loops contain P (even-odd per loop)
  _depthAt(t) {
    let e = 0;
    for (const s of this.loops) this._pointInLoop(t, s) && e++;
    return e;
  }
  // Clip a segment [P0,P1] by the collection of loops.
  // Returns an array of [t0, t1] with 0<=t0<t1<=1.
  ClipLine([t, e]) {
    const s = oo(e, t), i = dd(s);
    if (i < Gs) return [];
    const n = { x: s.x / i, y: s.y / i }, r = { x: -n.y, y: n.x }, o = 0, l = i, c = (f) => {
      const b = oo(f, t);
      return { x: ao(b, n), y: ao(b, r) };
    }, h = [];
    for (const f of this.loops)
      for (let b = 0; b + 1 < f.length; b++) {
        const y = c(f[b]), x = c(f[b + 1]), g = x.y - y.y;
        if (Math.abs(g) < Gs || !(y.y <= 0 && 0 < x.y || x.y <= 0 && 0 < y.y)) continue;
        const S = (0 - y.y) / g, Z = y.x + S * (x.x - y.x);
        h.push(Z);
      }
    if (!h.length) return [];
    h.sort((f, b) => f - b);
    const u = [];
    let d = null;
    for (const f of h)
      (d === null || Math.abs(f - d) > 1e-7) && u.push(f), d = f;
    const p = [];
    if (this.style === $t.ODD_PARITY) {
      for (let f = 0; f + 1 < u.length; f += 2) {
        const b = Math.max(o, u[f]), y = Math.min(l, u[f + 1]);
        y - b > 1e-9 && p.push([(b - o) / (l - o), (y - o) / (l - o)]);
      }
      return p;
    }
    const m = [o];
    for (const f of u) f > o + 1e-9 && f < l - 1e-9 && m.push(f);
    m.push(l);
    for (let f = 0; f + 1 < m.length; f++) {
      const b = m[f], y = m[f + 1];
      if (y - b <= 1e-9) continue;
      const x = (b + y) * 0.5, g = hd(t, ud(n, x)), S = this._depthAt(g);
      let Z = !1;
      this.style, $t.THROUGH_ENTIRE_AREA, Z = S >= 1, Z && p.push([(b - o) / (l - o), (y - o) / (l - o)]);
    }
    return p;
  }
}
class zc extends Gt {
  constructor(t, e) {
    super(t), this._font = e, this._lineEntity = new Is(t), this._splineEntity = new jr(t), this._patternHelper = {
      dir: new v(),
      nrm: new v(),
      offsetVec: new v(),
      base: new v()
    }, this._boxHelper = {
      min: new R(),
      max: new R()
    };
  }
  /**
   * It filters all the hatch entities and draw them.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  draw(t, e) {
    let s = new at();
    s.name = "HATCHES";
    let i = t.entities.filter((n) => n.type === "HATCH");
    if (i.length === 0) return null;
    for (let n = 0; n < i.length; n++) {
      let r = i[n];
      if (this._hideEntity(r)) continue;
      let o = this._getCached(r), l = null, c = null;
      if (o)
        l = o.geometry, c = o.material;
      else {
        let h = this.drawHatch(r, e);
        l = h.geometry, c = h.material, this._setCache(r, h);
      }
      if (l) {
        let h = r.fillType === "SOLID" ? new gt(l, c) : new Ts(l, c);
        h.userData = { entity: r }, h.renderOrder = r.fillType === "SOLID" ? -1 : 0, h.position.z = r.fillType === "SOLID" ? -0.1 : 0, s.add(h);
      }
    }
    return s;
  }
  /**
   * Draws a hatch entity.
   * @param entity {entity} dxf parsed hatch entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  drawHatch(t, e) {
    let s = null, i = null;
    switch (this._calculatePoints(t), t.boundary.loops.forEach((n) => this._setBoundaryTypes(n)), t.boundary.loops.sort((n, r) => n.bType.external != r.bType.external ? n.bType.external ? -1 : 1 : n.bType.outermost != r.bType.outermost ? n.bType.outermost ? -1 : 1 : 0), t.style) {
      case 2:
        t.boundary.loops = [t.boundary.loops[0]];
        break;
      case 1:
        t.boundary.loops = t.boundary.loops.filter((n) => n.bType.external || n.bType.outermost);
        break;
    }
    return this._calculateBoxes(t.boundary), t.fillType === "SOLID" ? (s = this._generateBoundary(t, e), i = this._colorHelper.getMaterial(t, "shape", this.data.tables)) : t.fillType === "PATTERN" && (s = this._generatePatternGeometry(t, e), i = this._colorHelper.getMaterial(t, "line", this.data.tables)), s && this._extrusionTransform(t, s), { geometry: s, material: i };
  }
  _calculatePoints(t) {
    const e = t.boundary;
    for (let s = 0; s < e.loops.length; s++) {
      const i = e.loops[s];
      for (let n = 0; n < i.entities.length; n++) {
        let r = i.entities[n];
        if (!(!r || !r.type))
          switch (r.type) {
            case "LINE":
              this._getLinePoints(r);
              break;
            case "POLYLINE":
              this._getPolyLinePoints(r);
              break;
            case "ARC":
              this._getArcPoints(r, t);
              break;
            case "ELLIPSE":
              this._getEllipsePoints(r);
              break;
            case "SPLINE":
              this._getSplinePoints(r);
              break;
          }
      }
    }
  }
  _getLinePoints(t) {
    t.points = [], t.points.push({ x: t.start.x, y: t.start.y, z: 0 }), t.points.push({ x: t.end.x, y: t.end.y, z: 0 });
  }
  _getPolyLinePoints() {
  }
  _getArcPoints(t, e) {
    t.points = [];
    let s = t.startAngle * Math.PI / 180, i = t.endAngle * Math.PI / 180;
    if (!t.counterClockWise && e.extrusionDir.z > 0) {
      const o = -s;
      s = -i, i = o;
    }
    let r = new Hr(
      t.center.x,
      t.center.y,
      t.radius,
      s,
      i,
      !1
    ).getPoints(32);
    for (let o = 0; o < r.length; o++) t.points.push({ x: r[o].x, y: r[o].y, z: 0 });
  }
  _getEllipsePoints(t) {
    t.points = [];
    let e = t.startAngle * Math.PI / 180, s = t.endAngle * Math.PI / 180, n = new Ae(
      t.center.x,
      t.center.y,
      t.major.x,
      t.major.y,
      e,
      s,
      !1,
      // Always counterclockwise
      t.minor
    ).getPoints(32);
    for (let r = 0; r < n.length; r++) t.points.push({ x: n[r].x, y: n[r].y, z: 0 });
  }
  _getSplinePoints(t) {
    t.points = this._splineEntity.getBSplinePolyline(
      t.controlPoints.points,
      t.degree,
      t.knots.knots,
      t.weights
    );
  }
  _generateBoundary(t) {
    const e = t.boundary, s = this._getBiggestLoop(e), i = e.loops.filter((o) => o !== s && this._isLoopHole(o, t.style)), n = this._mergeLoopPoints(s);
    if (n.length === 0) return null;
    const r = new te();
    r.setFromPoints(n);
    for (let o = 0; o < i.length; o++) {
      const l = this._mergeLoopPoints(i[o]);
      if (l.length === 0) continue;
      const c = new te();
      c.setFromPoints(l), r.holes.push(c);
    }
    return new Ke(r);
  }
  _calculateBoxes(t) {
    for (let e = 0; e < t.loops.length; e++) {
      const s = t.loops[e];
      s.box = this._getLoopBox(s);
    }
  }
  _getLoopBox(t) {
    let e = new R(Number.MAX_VALUE, Number.MAX_VALUE, Number.MAX_VALUE), s = new R(Number.MIN_VALUE, Number.MIN_VALUE, Number.MIN_VALUE);
    for (let i = 0; i < t.entities.length; i++) {
      const n = t.entities[i];
      for (let r = 0; r < n.points.length; r++) {
        const o = n.points[r];
        e.min(o), s.max(o);
      }
    }
    return new St(e, s);
  }
  _setBoundaryTypes(t) {
    t.bType = {
      external: (t.type & 1) != 0,
      polyline: (t.type & 2) != 0,
      derived: (t.type & 4) != 0,
      textbox: (t.type & 8) != 0,
      outermost: (t.type & 16) != 0
    }, t.eType = {
      polyline: typeof t.hasBulge < "u",
      line: typeof t.hasBulge < "u" ? t.hasBulge === 1 : t.edgeType === 1,
      circulararc: typeof t.hasBulge < "u" ? t.hasBulge === 2 : t.edgeType === 2,
      ellipticarc: typeof t.hasBulge < "u" ? t.hasBulge === 3 : t.edgeType === 3,
      spline: typeof t.hasBulge < "u" ? t.hasBulge === 4 : t.edgeType === 4
    };
  }
  _getBiggestLoop(t) {
    if (t.loops.length === 1) return t.loops[0];
    let e = t.loops[0], s = this._getLoopArea(e);
    for (let i = 1; i < t.loops.length; i++) {
      const n = t.loops[i], r = this._getLoopArea(n);
      r > s && (e = n, s = r);
    }
    return e;
  }
  _getLoopArea(t) {
    let e = 0;
    const s = t.box;
    return e = (s.max.x - s.min.x) * (s.max.y - s.min.y), e;
  }
  _mergeLoopPoints(t) {
    let e = [];
    const s = this._orderEntityPoints(t.entities);
    if (!s)
      return this.trigger("log", "loops with separated entities not supported yet"), e;
    for (let i = 0; i < s.length; i++) {
      const n = s[i];
      if (n)
        if (n.type === "POLYLINE") {
          let r = this._lineEntity._getPolyLinePoints(n.points, n.closed);
          e = e.concat(r);
        } else {
          if (!n.points) continue;
          let r = n.points.length > 0 ? n.points[n.points.length - 1] : null;
          for (let o = 0; o < n.points.length; o++) {
            const l = n.points[o];
            o === 0 && r && r.x === l.x && r.y === l.y || e.push(l);
          }
        }
    }
    return e;
  }
  _orderEntityPoints(t) {
    let e = [];
    const s = t.filter((n) => n.points && n.points.length > 0);
    if (s.length === 0) return [];
    e.push(s[0]);
    let i = s[0];
    for (; e.length < s.length; ) {
      let n = i.points[0], r = i.points[i.points.length - 1];
      const o = e.length;
      for (let l = e.length; l < s.length; l++) {
        const c = s[l];
        if (c) {
          if (this._samePoints(r, c.points[0])) {
            e.push(c), i = c;
            break;
          }
          if (this._samePoints(r, c.points[c.points.length - 1])) {
            c.points.reverse(), e.push(c), i = c;
            break;
          }
          if (this._samePoints(n, c.points[0])) {
            for (let h = 0; h < e.length; h++) e[h].points.reverse();
            e.push(c), i = c;
            break;
          }
          if (this._samePoints(n, c.points[c.points.length - 1])) {
            for (let h = 0; h < e.length; h++) e[h].points.reverse();
            c.points.reverse(), e.push(c), i = c;
            break;
          }
        }
      }
      if (o === e.length)
        return null;
    }
    return e;
  }
  _samePoints(t, e) {
    const s = t.x - e.x, i = t.y - e.y, n = t.z - e.z, r = s * s + i * i + n * n;
    return Math.sqrt(r) < 1e-4;
  }
  _isLoopHole(t, e) {
    return e === 1 ? (t.type & 16) === 16 && (t.type & 1) !== 1 : !0;
  }
  _extrusionTransform(t, e) {
    t.extrusionDir.z < 0 && e && e.scale(-1, 1, 1);
  }
  _generatePatternGeometry(t, e) {
    const s = t.pattern || {}, i = (s.angle || 0) * Math.PI / 180;
    this._patternHelper.dir.set(Math.cos(i), Math.sin(i)), this._patternHelper.nrm.set(-this._patternHelper.dir.y, this._patternHelper.dir.x);
    const n = this._patternHelper.dir, r = this._patternHelper.nrm;
    this._patternHelper.offsetVec.set(s.offsetX || 0, s.offsetY || 0);
    let o = Math.abs(this._patternHelper.offsetVec.x * r.x + this._patternHelper.offsetVec.y * r.y);
    o < 1e-9 && (o = t.spacing || 1), this._patternHelper.base.set(s.x || 0, s.y || 0);
    const l = this._patternHelper.base.dot(r), c = t.boundary;
    if (!c || !Array.isArray(c.loops) || c.loops.length === 0) return null;
    const h = (F) => F.map((X) => ({ x: X.x, y: X.y })), u = [];
    for (const F of c.loops) {
      let X = [];
      if (Array.isArray(F.references) && F.references.length) {
        const I = e(F.references), ot = (this._getPointsFromEntities(I) || []).filter(($) => $.length >= 2);
        X = this._stitchPolylines(ot);
      }
      if (X.length)
        for (const I of X)
          u.push(I);
      else {
        const I = this._mergeLoopPoints(F);
        if (I && I.length >= 3) {
          const H = this._cleanPolyline(h(I));
          H.length >= 3 && u.push(H);
        }
      }
    }
    if (u.length === 0) return null;
    let d = 1 / 0, p = -1 / 0, m = 1 / 0, f = -1 / 0;
    for (const F of u)
      for (const X of F) {
        const I = X.x * n.x + X.y * n.y, H = X.x * r.x + X.y * r.y;
        I < d && (d = I), I > p && (p = I), H < m && (m = H), H > f && (f = H);
      }
    const b = Math.ceil((m - l) / o), y = Math.floor((f - l) / o), x = { 0: $t.ODD_PARITY, 1: $t.OUTERMOST, 2: $t.THROUGH_ENTIRE_AREA }, g = new pd(u, x[t.style] ?? $t.ODD_PARITY), S = [], Z = 1, V = y - b;
    let G = V > 1e3 ? Math.ceil(V / 1e3) : 1;
    const M = new v(), L = new v();
    for (let F = b; F <= y; F += G) {
      const X = l + F * o, I = d - Z, H = p + Z;
      M.set(n.x * I + r.x * X, n.y * I + r.y * X), L.set(n.x * H + r.x * X, n.y * H + r.y * X);
      const ot = g.ClipLine([{ x: M.x, y: M.y }, { x: L.x, y: L.y }]);
      for (const [$, N] of ot) {
        const _ = M.clone().lerp(L, $), K = M.clone().lerp(L, N);
        S.push(_.x, _.y, 0, K.x, K.y, 0);
      }
    }
    if (!S.length) return null;
    const k = new rt();
    k.setAttribute("position", new ht(S, 3));
    const w = k.attributes.position.count, E = [];
    for (let F = 0; F < w; F += 2)
      E.push(F, F + 1);
    return k.setIndex(new q(new Uint16Array(E), 1)), k;
  }
  _stitchPolylines(t) {
    const s = (r, o) => Math.abs(r.x - o.x) < 1e-6 && Math.abs(r.y - o.y) < 1e-6, i = t.map(this._cleanPolyline).filter((r) => r.length > 0), n = [];
    for (; i.length; ) {
      let r = i.shift().slice(), o = !0;
      for (; o; ) {
        o = !1;
        for (let l = 0; l < i.length; l++) {
          const c = i[l], h = r[0], u = r[r.length - 1], d = c[0], p = c[c.length - 1];
          if (s(u, d)) {
            r = r.concat(c.slice(1)), i.splice(l, 1), o = !0;
            break;
          }
          if (s(u, p)) {
            r = r.concat(c.slice(0, -1).reverse()), i.splice(l, 1), o = !0;
            break;
          }
          if (s(h, p)) {
            r = c.slice(0, -1).concat(r), i.splice(l, 1), o = !0;
            break;
          }
          if (s(h, d)) {
            r = c.slice(1).reverse().concat(r), i.splice(l, 1), o = !0;
            break;
          }
        }
      }
      r.length >= 3 && s(r[0], r[r.length - 1]) && r.pop(), r.length >= 3 && n.push(r);
    }
    return n;
  }
  _cleanPolyline(t) {
    if (!t || t.length < 2) return [];
    const e = 1e-6, s = (n, r) => Math.abs(n.x - r.x) < e && Math.abs(n.y - r.y) < e, i = [t[0]];
    for (let n = 1; n < t.length; n++) {
      const r = t[n], o = i[i.length - 1];
      s(r, o) || i.push(r);
    }
    return i.length >= 3 && s(i[0], i[i.length - 1]) && i.pop(), i;
  }
  /**
   * Convert a THREE.BufferGeometry (indexed or not) into one or more polylines.
   * Each polyline is returned as an array of THREE.Vector2.
   * - If geometry has an index, we follow the index order and split when the
   *   path breaks (non-consecutive jump).
   * - If no index, we read the position array in order.
   * - If the geometry represents triangles, this still works because we split
   *   on breaks; for clean results you should pass line/outline geometries.
   */
  _geometryToVector2Arrays(t) {
    if (!t) return [];
    const e = t.getAttribute("position");
    if (!e) return [];
    const s = e.array, i = e.itemSize || 3, n = (o) => {
      const l = o * i;
      return { x: s[l], y: s[l + 1] };
    }, r = [];
    if (t.index && t.index.count) {
      const o = t.index.array;
      let l = [], c = -1;
      for (let h = 0; h < o.length; h++) {
        const u = o[h];
        c >= 0 && Math.abs(u - c) > 1 && (l.length > 1 && r.push(l), l = []), l.push(n(u)), c = u;
      }
      l.length > 1 && r.push(l);
    } else {
      const o = Math.floor(s.length / i), l = [];
      for (let c = 0; c < o; c++) l.push(n(c));
      l.length > 1 && r.push(l);
    }
    return r;
  }
  /**
   * Convert an array of objects that contain `.geometry` into Vector2[][].
   * Each entry may look like { geometry: BufferGeometry, ... }.
   * You can pass in any extra fields – they’re ignored.
   */
  _getPointsFromEntities(t) {
    const e = [];
    if (!Array.isArray(t)) return e;
    for (const s of t) {
      if (!s || !s.geometry) continue;
      const i = this._geometryToVector2Arrays(s.geometry);
      for (const n of i) n.length > 1 && e.push(n);
    }
    return e;
  }
}
const md = "data:text/javascript;base64,IWZ1bmN0aW9uKHQsZSl7Im9iamVjdCI9PXR5cGVvZiBleHBvcnRzJiYib2JqZWN0Ij09dHlwZW9mIG1vZHVsZT9tb2R1bGUuZXhwb3J0cz1lKCk6ImZ1bmN0aW9uIj09dHlwZW9mIGRlZmluZSYmZGVmaW5lLmFtZD9kZWZpbmUoW10sZSk6Im9iamVjdCI9PXR5cGVvZiBleHBvcnRzP2V4cG9ydHMuV01GSlM9ZSgpOnQuV01GSlM9ZSgpfSh0aGlzLCgoKT0+KCgpPT57InVzZSBzdHJpY3QiO3ZhciB0PXtkOihlLGkpPT57Zm9yKHZhciBzIGluIGkpdC5vKGkscykmJiF0Lm8oZSxzKSYmT2JqZWN0LmRlZmluZVByb3BlcnR5KGUscyx7ZW51bWVyYWJsZTohMCxnZXQ6aVtzXX0pfSxvOih0LGUpPT5PYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwodCxlKSxyOnQ9PnsidW5kZWZpbmVkIiE9dHlwZW9mIFN5bWJvbCYmU3ltYm9sLnRvU3RyaW5nVGFnJiZPYmplY3QuZGVmaW5lUHJvcGVydHkodCxTeW1ib2wudG9TdHJpbmdUYWcse3ZhbHVlOiJNb2R1bGUifSksT2JqZWN0LmRlZmluZVByb3BlcnR5KHQsIl9fZXNNb2R1bGUiLHt2YWx1ZTohMH0pfX0sZT17fTt0LnIoZSksdC5kKGUse0Vycm9yOigpPT5hLFJlbmRlcmVyOigpPT54LGxvZ2dpbmdFbmFibGVkOigpPT5sfSk7dmFyIGkscz1mdW5jdGlvbigpe2Z1bmN0aW9uIHQoKXt9cmV0dXJuIHQucHJvdG90eXBlLmZsb29kPWZ1bmN0aW9uKHQsZSxpLHMsbil7dmFyIG89ZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TKCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIsImZlRmxvb2QiKTtlJiZvLnNldEF0dHJpYnV0ZSgiaWQiLGUpLG8uc2V0QXR0cmlidXRlKCJmbG9vZC1jb2xvciIsaSksby5zZXRBdHRyaWJ1dGUoImZsb29kLW9wYWNpdHkiLHMudG9TdHJpbmcoKSksdC5hcHBlbmRDaGlsZChvKX0sdC5wcm90b3R5cGUuY29tcG9zaXRlPWZ1bmN0aW9uKHQsZSxpLHMsbixvLHIsYSxoKXt2YXIgbD1kb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiwiZmVDb21wb3NpdGUiKTtlJiZsLnNldEF0dHJpYnV0ZSgiaWQiLGUpLGwuc2V0QXR0cmlidXRlKCJpbiIsaSksbC5zZXRBdHRyaWJ1dGUoImluMiIscyksdC5hcHBlbmRDaGlsZChsKX0sdH0oKSxuPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCgpe3RoaXMuX3BhdGg9IiJ9cmV0dXJuIHQucHJvdG90eXBlLm1vdmU9ZnVuY3Rpb24odCxlKXt0aGlzLl9wYXRoKz0iIE0gIi5jb25jYXQodCwiICIpLmNvbmNhdChlKX0sdC5wcm90b3R5cGUucGF0aD1mdW5jdGlvbigpe3JldHVybiB0aGlzLl9wYXRoLnN1YnN0cigxKX0sdC5wcm90b3R5cGUubGluZT1mdW5jdGlvbih0KXt2YXIgZT10aGlzO3QuZm9yRWFjaCgoZnVuY3Rpb24odCl7ZS5fcGF0aCs9IiBMICIuY29uY2F0KHRbMF0sIiAiKS5jb25jYXQodFsxXSl9KSl9LHQucHJvdG90eXBlLmN1cnZlQz1mdW5jdGlvbih0LGUsaSxzLG4sbyl7dGhpcy5fcGF0aCs9IiBDICIuY29uY2F0KHQsIiAiKS5jb25jYXQoZSwiLCAiKS5jb25jYXQoaSwiICIpLmNvbmNhdChzLCIsICIpLmNvbmNhdChuLCIgIikuY29uY2F0KG8pfSx0LnByb3RvdHlwZS5jbG9zZT1mdW5jdGlvbigpe3RoaXMuX3BhdGgrPSIgWiJ9LHR9KCksbz1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCl7dGhpcy5maWx0ZXJzPW5ldyBzLHRoaXMuX2RlZnM9dm9pZCAwLHRoaXMuX3N2Zz10fXJldHVybiB0LnByb3RvdHlwZS5zdmc9ZnVuY3Rpb24odCxlLGkscyxuLG8pe3ZhciByPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJzdmciKTtyZXR1cm4gci5zZXRBdHRyaWJ1dGUoIngiLGUudG9TdHJpbmcoKSksci5zZXRBdHRyaWJ1dGUoInkiLGkudG9TdHJpbmcoKSksci5zZXRBdHRyaWJ1dGUoIndpZHRoIixzLnRvU3RyaW5nKCkpLHIuc2V0QXR0cmlidXRlKCJoZWlnaHQiLG4udG9TdHJpbmcoKSksdGhpcy5fYXBwZW5kU2V0dGluZ3MobyxyKSxudWxsIT10P3QuYXBwZW5kQ2hpbGQocik6dGhpcy5fc3ZnLmFwcGVuZENoaWxkKHIpLHJ9LHQucHJvdG90eXBlLmltYWdlPWZ1bmN0aW9uKHQsZSxpLHMsbixvLHIpe3ZhciBhPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJpbWFnZSIpO3JldHVybiBhLnNldEF0dHJpYnV0ZSgieCIsZS50b1N0cmluZygpKSxhLnNldEF0dHJpYnV0ZSgieSIsaS50b1N0cmluZygpKSxhLnNldEF0dHJpYnV0ZSgid2lkdGgiLHMudG9TdHJpbmcoKSksYS5zZXRBdHRyaWJ1dGUoImhlaWdodCIsbi50b1N0cmluZygpKSxhLnNldEF0dHJpYnV0ZU5TKCJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiwiaHJlZiIsbyksdGhpcy5fYXBwZW5kU2V0dGluZ3MocixhKSx0LmFwcGVuZENoaWxkKGEpLGF9LHQucHJvdG90eXBlLnJlY3Q9ZnVuY3Rpb24odCxlLGkscyxuLG8scixhKXt2YXIgaD1kb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiwicmVjdCIpO3JldHVybiBoLnNldEF0dHJpYnV0ZSgieCIsZS50b1N0cmluZygpKSxoLnNldEF0dHJpYnV0ZSgieSIsaS50b1N0cmluZygpKSxoLnNldEF0dHJpYnV0ZSgid2lkdGgiLHMudG9TdHJpbmcoKSksaC5zZXRBdHRyaWJ1dGUoImhlaWdodCIsbi50b1N0cmluZygpKSx2b2lkIDAhPT1vJiYobyBpbnN0YW5jZW9mIE51bWJlcj9oLnNldEF0dHJpYnV0ZSgicngiLG8udG9TdHJpbmcoKSk6byBpbnN0YW5jZW9mIE9iamVjdCYmdGhpcy5fYXBwZW5kU2V0dGluZ3MobyxoKSksdm9pZCAwIT09ciYmaC5zZXRBdHRyaWJ1dGUoInJ5IixyLnRvU3RyaW5nKCkpLHRoaXMuX2FwcGVuZFNldHRpbmdzKGEsaCksdC5hcHBlbmRDaGlsZChoKSxofSx0LnByb3RvdHlwZS5saW5lPWZ1bmN0aW9uKHQsZSxpLHMsbixvKXt2YXIgcj1kb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiwibGluZSIpO3JldHVybiByLnNldEF0dHJpYnV0ZSgieDEiLGUudG9TdHJpbmcoKSksci5zZXRBdHRyaWJ1dGUoInkxIixpLnRvU3RyaW5nKCkpLHIuc2V0QXR0cmlidXRlKCJ4MiIscy50b1N0cmluZygpKSxyLnNldEF0dHJpYnV0ZSgieTIiLG4udG9TdHJpbmcoKSksdGhpcy5fYXBwZW5kU2V0dGluZ3MobyxyKSx0LmFwcGVuZENoaWxkKHIpLHJ9LHQucHJvdG90eXBlLnBvbHlnb249ZnVuY3Rpb24odCxlLGkpe3ZhciBzPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJwb2x5Z29uIik7cmV0dXJuIHMuc2V0QXR0cmlidXRlKCJwb2ludHMiLGUubWFwKChmdW5jdGlvbih0KXtyZXR1cm4gdC5qb2luKCIsIil9KSkuam9pbigiICIpKSx0aGlzLl9hcHBlbmRTZXR0aW5ncyhpLHMpLHQuYXBwZW5kQ2hpbGQocyksc30sdC5wcm90b3R5cGUucG9seWxpbmU9ZnVuY3Rpb24odCxlLGkpe3ZhciBzPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJwb2x5bGluZSIpO3JldHVybiBzLnNldEF0dHJpYnV0ZSgicG9pbnRzIixlLm1hcCgoZnVuY3Rpb24odCl7cmV0dXJuIHQuam9pbigiLCIpfSkpLmpvaW4oIiAiKSksdGhpcy5fYXBwZW5kU2V0dGluZ3MoaSxzKSx0LmFwcGVuZENoaWxkKHMpLHN9LHQucHJvdG90eXBlLmVsbGlwc2U9ZnVuY3Rpb24odCxlLGkscyxuLG8pe3ZhciByPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJlbGxpcHNlIik7cmV0dXJuIHIuc2V0QXR0cmlidXRlKCJjeCIsZS50b1N0cmluZygpKSxyLnNldEF0dHJpYnV0ZSgiY3kiLGkudG9TdHJpbmcoKSksci5zZXRBdHRyaWJ1dGUoInJ4IixzLnRvU3RyaW5nKCkpLHIuc2V0QXR0cmlidXRlKCJyeSIsbi50b1N0cmluZygpKSx0aGlzLl9hcHBlbmRTZXR0aW5ncyhvLHIpLHQuYXBwZW5kQ2hpbGQocikscn0sdC5wcm90b3R5cGUucGF0aD1mdW5jdGlvbih0LGUsaSl7dmFyIHM9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TKCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIsInBhdGgiKTtyZXR1cm4gcy5zZXRBdHRyaWJ1dGUoImQiLGUucGF0aCgpKSx0aGlzLl9hcHBlbmRTZXR0aW5ncyhpLHMpLHQuYXBwZW5kQ2hpbGQocyksc30sdC5wcm90b3R5cGUudGV4dD1mdW5jdGlvbih0LGUsaSxzLG4pe3ZhciBvPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJ0ZXh0Iik7by5zZXRBdHRyaWJ1dGUoIngiLGUudG9TdHJpbmcoKSksby5zZXRBdHRyaWJ1dGUoInkiLGkudG9TdHJpbmcoKSksdGhpcy5fYXBwZW5kU2V0dGluZ3MobixvKTt2YXIgcj1kb2N1bWVudC5jcmVhdGVUZXh0Tm9kZShzKTtyZXR1cm4gby5hcHBlbmRDaGlsZChyKSx0LmFwcGVuZENoaWxkKG8pLG99LHQucHJvdG90eXBlLmZpbHRlcj1mdW5jdGlvbih0LGUsaSxzLG4sbyxyKXt2YXIgYT1kb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiwiZmlsdGVyIik7cmV0dXJuIGEuc2V0QXR0cmlidXRlKCJ4IixpLnRvU3RyaW5nKCkpLGEuc2V0QXR0cmlidXRlKCJ5IixzLnRvU3RyaW5nKCkpLGEuc2V0QXR0cmlidXRlKCJ3aWR0aCIsbi50b1N0cmluZygpKSxhLnNldEF0dHJpYnV0ZSgiaGVpZ2h0IixvLnRvU3RyaW5nKCkpLHRoaXMuX2FwcGVuZFNldHRpbmdzKHIsYSksdC5hcHBlbmRDaGlsZChhKSxhfSx0LnByb3RvdHlwZS5wYXR0ZXJuPWZ1bmN0aW9uKHQsZSxpLHMsbixvLHIpe3ZhciBhPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJwYXR0ZXJuIik7cmV0dXJuIGUmJmEuc2V0QXR0cmlidXRlKCJpZCIsZSksYS5zZXRBdHRyaWJ1dGUoIngiLGkudG9TdHJpbmcoKSksYS5zZXRBdHRyaWJ1dGUoInkiLHMudG9TdHJpbmcoKSksYS5zZXRBdHRyaWJ1dGUoIndpZHRoIixuLnRvU3RyaW5nKCkpLGEuc2V0QXR0cmlidXRlKCJoZWlnaHQiLG8udG9TdHJpbmcoKSksdGhpcy5fYXBwZW5kU2V0dGluZ3MocixhKSx0LmFwcGVuZENoaWxkKGEpLGF9LHQucHJvdG90eXBlLmRlZnM9ZnVuY3Rpb24oKXtpZih2b2lkIDA9PT10aGlzLl9kZWZzKXt2YXIgdD1kb2N1bWVudC5jcmVhdGVFbGVtZW50TlMoImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiwiZGVmcyIpO3RoaXMuX3N2Zy5hcHBlbmRDaGlsZCh0KSx0aGlzLl9kZWZzPXR9cmV0dXJuIHRoaXMuX2RlZnN9LHQucHJvdG90eXBlLmNsaXBQYXRoPWZ1bmN0aW9uKHQsZSxpLHMpe3ZhciBuPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUygiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciLCJjbGlwUGF0aCIpO3JldHVybiBlJiZuLnNldEF0dHJpYnV0ZSgiaWQiLGUpLHZvaWQgMD09PWkmJihpPSJ1c2VyU3BhY2VPblVzZSIpLG4uc2V0QXR0cmlidXRlKCJjbGlwUGF0aFVuaXRzIixpKSx0aGlzLl9hcHBlbmRTZXR0aW5ncyhzLG4pLHQuYXBwZW5kQ2hpbGQobiksbn0sdC5wcm90b3R5cGUuY3JlYXRlUGF0aD1mdW5jdGlvbigpe3JldHVybiBuZXcgbn0sdC5wcm90b3R5cGUuX2FwcGVuZFNldHRpbmdzPWZ1bmN0aW9uKHQsZSl7dm9pZCAwIT09dCYmT2JqZWN0LmtleXModCkuZm9yRWFjaCgoZnVuY3Rpb24oaSl7ZS5zZXRBdHRyaWJ1dGUoaSx0W2ldKX0pKX0sdH0oKSxyPShpPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGk9T2JqZWN0LnNldFByb3RvdHlwZU9mfHx7X19wcm90b19fOltdfWluc3RhbmNlb2YgQXJyYXkmJmZ1bmN0aW9uKHQsZSl7dC5fX3Byb3RvX189ZX18fGZ1bmN0aW9uKHQsZSl7Zm9yKHZhciBpIGluIGUpT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGUsaSkmJih0W2ldPWVbaV0pfSxpKHQsZSl9LGZ1bmN0aW9uKHQsZSl7aWYoImZ1bmN0aW9uIiE9dHlwZW9mIGUmJm51bGwhPT1lKXRocm93IG5ldyBUeXBlRXJyb3IoIkNsYXNzIGV4dGVuZHMgdmFsdWUgIitTdHJpbmcoZSkrIiBpcyBub3QgYSBjb25zdHJ1Y3RvciBvciBudWxsIik7ZnVuY3Rpb24gcygpe3RoaXMuY29uc3RydWN0b3I9dH1pKHQsZSksdC5wcm90b3R5cGU9bnVsbD09PWU/T2JqZWN0LmNyZWF0ZShlKToocy5wcm90b3R5cGU9ZS5wcm90b3R5cGUsbmV3IHMpfSksYT1mdW5jdGlvbih0KXtmdW5jdGlvbiBlKGUpe3ZhciBpPXRoaXMuY29uc3RydWN0b3Iscz10LmNhbGwodGhpcyxlKXx8dGhpcztyZXR1cm4gT2JqZWN0LnNldFByb3RvdHlwZU9mKHMsaS5wcm90b3R5cGUpLHN9cmV0dXJuIHIoZSx0KSxlfShFcnJvciksaD0hMDtmdW5jdGlvbiBsKHQpe2g9dH12YXIgYz1mdW5jdGlvbigpe2Z1bmN0aW9uIHQoKXt9cmV0dXJuIHQubG9nPWZ1bmN0aW9uKHQpe2gmJmNvbnNvbGUubG9nKHQpfSx0Ll9tYWtlVW5pcXVlSWQ9ZnVuY3Rpb24odCl7cmV0dXJuIndtZmpzXyIrdCt0aGlzLl91bmlxdWVJZCsrfSx0Ll93cml0ZVVpbnQzMlZhbD1mdW5jdGlvbih0LGUsaSl7dFtlKytdPTI1NSZpLHRbZSsrXT1pPj4+OCYyNTUsdFtlKytdPWk+Pj4xNiYyNTUsdFtlKytdPWk+Pj4yNCYyNTV9LHQuX2Jsb2JUb0JpbmFyeT1mdW5jdGlvbih0KXtmb3IodmFyIGU9IiIsaT10Lmxlbmd0aCxzPTA7czxpO3MrKyllKz1TdHJpbmcuZnJvbUNoYXJDb2RlKHRbc10pO3JldHVybiBlfSx0LkdEST17TUVUQUhFQURFUl9TSVpFOjE4LEJJVE1BUElORk9IRUFERVJfU0laRTo0MCxCSVRNQVBDT1JFSEVBREVSX1NJWkU6MTIsTWV0YWZpbGVUeXBlOntNRU1PUllNRVRBRklMRToxLERJU0tNRVRBRklMRToyfSxNZXRhZmlsZVZlcnNpb246e01FVEFWRVJTSU9OMTAwOjI1NixNRVRBVkVSU0lPTjMwMDo3Njh9LFJlY29yZFR5cGU6e01FVEFfRU9GOjAsTUVUQV9SRUFMSVpFUEFMRVRURTo1MyxNRVRBX1NFVFBBTEVOVFJJRVM6NTUsTUVUQV9TRVRCS01PREU6MjU4LE1FVEFfU0VUTUFQTU9ERToyNTksTUVUQV9TRVRST1AyOjI2MCxNRVRBX1NFVFJFTEFCUzoyNjEsTUVUQV9TRVRQT0xZRklMTE1PREU6MjYyLE1FVEFfU0VUU1RSRVRDSEJMVE1PREU6MjYzLE1FVEFfU0VUVEVYVENIQVJFWFRSQToyNjQsTUVUQV9SRVNUT1JFREM6Mjk1LE1FVEFfUkVTSVpFUEFMRVRURTozMTMsTUVUQV9ESUJDUkVBVEVQQVRURVJOQlJVU0g6MzIyLE1FVEFfU0VUTEFZT1VUOjMyOSxNRVRBX1NFVEJLQ09MT1I6NTEzLE1FVEFfU0VUVEVYVENPTE9SOjUyMSxNRVRBX09GRlNFVFZJRVdQT1JUT1JHOjUyOSxNRVRBX0xJTkVUTzo1MzEsTUVUQV9NT1ZFVE86NTMyLE1FVEFfT0ZGU0VUQ0xJUFJHTjo1NDQsTUVUQV9GSUxMUkVHSU9OOjU1MixNRVRBX1NFVE1BUFBFUkZMQUdTOjU2MSxNRVRBX1NFTEVDVFBBTEVUVEU6NTY0LE1FVEFfUE9MWUdPTjo4MDQsTUVUQV9QT0xZTElORTo4MDUsTUVUQV9TRVRURVhUSlVTVElGSUNBVElPTjo1MjIsTUVUQV9TRVRXSU5ET1dPUkc6NTIzLE1FVEFfU0VUV0lORE9XRVhUOjUyNCxNRVRBX1NFVFZJRVdQT1JUT1JHOjUyNSxNRVRBX1NFVFZJRVdQT1JURVhUOjUyNixNRVRBX09GRlNFVFdJTkRPV09SRzo1MjcsTUVUQV9TQ0FMRVdJTkRPV0VYVDoxMDQwLE1FVEFfU0NBTEVWSUVXUE9SVEVYVDoxMDQyLE1FVEFfRVhDTFVERUNMSVBSRUNUOjEwNDUsTUVUQV9JTlRFUlNFQ1RDTElQUkVDVDoxMDQ2LE1FVEFfRUxMSVBTRToxMDQ4LE1FVEFfRkxPT0RGSUxMOjEwNDksTUVUQV9GUkFNRVJFR0lPTjoxMDY1LE1FVEFfQU5JTUFURVBBTEVUVEU6MTA3OCxNRVRBX1RFWFRPVVQ6MTMxMyxNRVRBX1BPTFlQT0xZR09OOjEzMzYsTUVUQV9FWFRGTE9PREZJTEw6MTM1MixNRVRBX1JFQ1RBTkdMRToxMDUxLE1FVEFfU0VUUElYRUw6MTA1NSxNRVRBX1JPVU5EUkVDVDoxNTY0LE1FVEFfUEFUQkxUOjE1NjUsTUVUQV9TQVZFREM6MzAsTUVUQV9QSUU6MjA3NCxNRVRBX1NUUkVUQ0hCTFQ6Mjg1MSxNRVRBX0VTQ0FQRToxNTc0LE1FVEFfSU5WRVJUUkVHSU9OOjI5OCxNRVRBX1BBSU5UUkVHSU9OOjI5OSxNRVRBX1NFTEVDVENMSVBSRUdJT046MzAwLE1FVEFfU0VMRUNUT0JKRUNUOjMwMSxNRVRBX1NFVFRFWFRBTElHTjozMDIsTUVUQV9BUkM6MjA3MSxNRVRBX0NIT1JEOjIwOTYsTUVUQV9CSVRCTFQ6MjMzOCxNRVRBX0VYVFRFWFRPVVQ6MjYxMCxNRVRBX1NFVERJQlRPREVWOjMzNzksTUVUQV9ESUJCSVRCTFQ6MjM2OCxNRVRBX0RJQlNUUkVUQ0hCTFQ6Mjg4MSxNRVRBX1NUUkVUQ0hESUI6MzkwNyxNRVRBX0RFTEVURU9CSkVDVDo0OTYsTUVUQV9DUkVBVEVQQUxFVFRFOjI0NyxNRVRBX0NSRUFURVBBVFRFUk5CUlVTSDo1MDUsTUVUQV9DUkVBVEVQRU5JTkRJUkVDVDo3NjIsTUVUQV9DUkVBVEVGT05USU5ESVJFQ1Q6NzYzLE1FVEFfQ1JFQVRFQlJVU0hJTkRJUkVDVDo3NjQsTUVUQV9DUkVBVEVSRUdJT046MTc5MX0sTWV0YWZpbGVFc2NhcGVzOntORVdGUkFNRToxLEFCT1JURE9DOjIsTkVYVEJBTkQ6MyxTRVRDT0xPUlRBQkxFOjQsR0VUQ09MT1JUQUJMRTo1LEZMVVNIT1VUOjYsRFJBRlRNT0RFOjcsUVVFUllFU0NTVVBQT1JUOjgsU0VUQUJPUlRQUk9DOjksU1RBUlRET0M6MTAsRU5ERE9DOjExLEdFVFBIWVNQQUdFU0laRToxMixHRVRQUklOVElOR09GRlNFVDoxMyxHRVRTQ0FMSU5HRkFDVE9SOjE0LE1FVEFfRVNDQVBFX0VOSEFOQ0VEX01FVEFGSUxFOjE1LFNFVFBFTldJRFRIOjE2LFNFVENPUFlDT1VOVDoxNyxTRVRQQVBFUlNPVVJDRToxOCxQQVNTVEhST1VHSDoxOSxHRVRURUNITk9MT0dZOjIwLFNFVExJTkVDQVA6MjEsU0VUTElORUpPSU46MjIsU0VUTUlURVJMSU1JVDoyMyxCQU5ESU5GTzoyNCxEUkFXUEFUVEVSTlJFQ1Q6MjUsR0VUVkVDVE9SUEVOU0laRToyNixHRVRWRUNUT1JCUlVTSFNJWkU6MjcsRU5BQkxFRFVQTEVYOjI4LEdFVFNFVFBBUEVSQklOUzoyOSxHRVRTRVRQUklOVE9SSUVOVDozMCxFTlVNUEFQRVJCSU5TOjMxLFNFVERJQlNDQUxJTkc6MzIsRVBTUFJJTlRJTkc6MzMsRU5VTVBBUEVSTUVUUklDUzozNCxHRVRTRVRQQVBFUk1FVFJJQ1M6MzUsUE9TVFNDUklQVF9EQVRBOjM3LFBPU1RTQ1JJUFRfSUdOT1JFOjM4LEdFVERFVklDRVVOSVRTOjQyLEdFVEVYVEVOREVEVEVYVE1FVFJJQ1M6MjU2LEdFVFBBSVJLRVJOVEFCTEU6MjU4LEVYVFRFWFRPVVQ6NTEyLEdFVEZBQ0VOQU1FOjUxMyxET1dOTE9BREZBQ0U6NTE0LE1FVEFGSUxFX0RSSVZFUjoyMDQ5LFFVRVJZRElCU1VQUE9SVDozMDczLEJFR0lOX1BBVEg6NDA5NixDTElQX1RPX1BBVEg6NDA5NyxFTkRfUEFUSDo0MDk4LE9QRU5fQ0hBTk5FTDo0MTEwLERPV05MT0FESEVBREVSOjQxMTEsQ0xPU0VfQ0hBTk5FTDo0MTEyLFBPU1RTQ1JJUFRfUEFTU1RIUk9VR0g6NDExNSxFTkNBUFNVTEFURURfUE9TVFNDUklQVDo0MTE2LFBPU1RTQ1JJUFRfSURFTlRJRlk6NDExNyxQT1NUU0NSSVBUX0lOSkVDVElPTjo0MTE4LENIRUNLSlBFR0ZPUk1BVDo0MTE5LENIRUNLUE5HRk9STUFUOjQxMjAsR0VUX1BTX0ZFQVRVUkVTRVRUSU5HOjQxMjEsTVhEQ19FU0NBUEU6NDEyMixTUENMUEFTU1RIUk9VR0gyOjQ1Njh9LE1hcE1vZGU6e01NX1RFWFQ6MSxNTV9MT01FVFJJQzoyLE1NX0hJTUVUUklDOjMsTU1fTE9FTkdMSVNIOjQsTU1fSElFTkdMSVNIOjUsTU1fVFdJUFM6NixNTV9JU09UUk9QSUM6NyxNTV9BTklTT1RST1BJQzo4fSxTdHJldGNoTW9kZTp7QkxBQ0tPTldISVRFOjEsV0hJVEVPTkJMQUNLOjIsQ09MT1JPTkNPTE9SOjMsSEFMRlRPTkU6NH0sVGV4dEFsaWdubWVudE1vZGU6e1RBX1VQREFURUNQOjEsVEFfUklHSFQ6MixUQV9DRU5URVI6NixUQV9CT1RUT006OCxUQV9CQVNFTElORToyNCxUQV9SVExSRUFESU5HOjI1Nn0sTWl4TW9kZTp7VFJBTlNQQVJFTlQ6MSxPUEFRVUU6Mn0sVmVydGljYWxUZXh0QWxpZ25tZW50TW9kZTp7VlRBX0JPVFRPTToyLFZUQV9DRU5URVI6NixWVEFfTEVGVDo4LFZUQV9CQVNFTElORToyNH0sQnJ1c2hTdHlsZTp7QlNfU09MSUQ6MCxCU19OVUxMOjEsQlNfSEFUQ0hFRDoyLEJTX1BBVFRFUk46MyxCU19JTkRFWEVEOjQsQlNfRElCUEFUVEVSTjo1LEJTX0RJQlBBVFRFUk5QVDo2LEJTX1BBVFRFUk44WDg6NyxCU19ESUJQQVRURVJOOFg4OjgsQlNfTU9OT1BBVFRFUk46OX0sUGVuU3R5bGU6e1BTX1NPTElEOjAsUFNfREFTSDoxLFBTX0RPVDoyLFBTX0RBU0hET1Q6MyxQU19EQVNIRE9URE9UOjQsUFNfTlVMTDo1LFBTX0lOU0lERUZSQU1FOjYsUFNfVVNFUlNUWUxFOjcsUFNfQUxURVJOQVRFOjgsUFNfRU5EQ0FQX1NRVUFSRToyNTYsUFNfRU5EQ0FQX0ZMQVQ6NTEyLFBTX0pPSU5fQkVWRUw6NDA5NixQU19KT0lOX01JVEVSOjgxOTJ9LFBvbHlGaWxsTW9kZTp7QUxURVJOQVRFOjEsV0lORElORzoyfSxDb2xvclVzYWdlOntESUJfUkdCX0NPTE9SUzowLERJQl9QQUxfQ09MT1JTOjEsRElCX1BBTF9JTkRJQ0VTOjJ9LFBhbGV0dGVFbnRyeUZsYWc6e1BDX1JFU0VSVkVEOjEsUENfRVhQTElDSVQ6MixQQ19OT0NPTExBUFNFOjR9LEJpdG1hcENvbXByZXNzaW9uOntCSV9SR0I6MCxCSV9STEU4OjEsQklfUkxFNDoyLEJJX0JJVEZJRUxEUzozLEJJX0pQRUc6NCxCSV9QTkc6NX19LHQuX3VuaXF1ZUlkPTAsdH0oKSxwPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdChlLGkpe2UgaW5zdGFuY2VvZiB0Pyh0aGlzLmJsb2I9ZS5ibG9iLHRoaXMuZGF0YT1lLmRhdGEsdGhpcy5wb3M9aXx8ZS5wb3MpOih0aGlzLmJsb2I9ZSx0aGlzLmRhdGE9bmV3IFVpbnQ4QXJyYXkoZSksdGhpcy5wb3M9aXx8MCl9cmV0dXJuIHQucHJvdG90eXBlLmVvZj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnBvcz49dGhpcy5kYXRhLmxlbmd0aH0sdC5wcm90b3R5cGUuc2Vlaz1mdW5jdGlvbih0KXtpZih0PDB8fHQ+dGhpcy5kYXRhLmxlbmd0aCl0aHJvdyBuZXcgYSgiSW52YWxpZCBzZWVrIHBvc2l0aW9uIik7dGhpcy5wb3M9dH0sdC5wcm90b3R5cGUuc2tpcD1mdW5jdGlvbih0KXt2YXIgZT10aGlzLnBvcyt0O2lmKGU+dGhpcy5kYXRhLmxlbmd0aCl0aHJvdyBuZXcgYSgiVW5leHBlY3RlZCBlbmQgb2YgZmlsZSIpO3RoaXMucG9zPWV9LHQucHJvdG90eXBlLnJlYWRCaW5hcnk9ZnVuY3Rpb24odCl7aWYodGhpcy5wb3MrdD50aGlzLmRhdGEubGVuZ3RoKXRocm93IG5ldyBhKCJVbmV4cGVjdGVkIGVuZCBvZiBmaWxlIik7Zm9yKHZhciBlPSIiO3QtLSA+MDspZSs9U3RyaW5nLmZyb21DaGFyQ29kZSh0aGlzLmRhdGFbdGhpcy5wb3MrK10pO3JldHVybiBlfSx0LnByb3RvdHlwZS5yZWFkSW50OD1mdW5jdGlvbigpe2lmKHRoaXMucG9zKzE+dGhpcy5kYXRhLmxlbmd0aCl0aHJvdyBuZXcgYSgiVW5leHBlY3RlZCBlbmQgb2YgZmlsZSIpO3JldHVybiB0aGlzLmRhdGFbdGhpcy5wb3MrK119LHQucHJvdG90eXBlLnJlYWRVaW50OD1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlYWRJbnQ4KCk+Pj4wfSx0LnByb3RvdHlwZS5yZWFkSW50MzI9ZnVuY3Rpb24oKXtpZih0aGlzLnBvcys0PnRoaXMuZGF0YS5sZW5ndGgpdGhyb3cgbmV3IGEoIlVuZXhwZWN0ZWQgZW5kIG9mIGZpbGUiKTt2YXIgdD10aGlzLmRhdGFbdGhpcy5wb3MrK107cmV0dXJuIHR8PXRoaXMuZGF0YVt0aGlzLnBvcysrXTw8OCwodHw9dGhpcy5kYXRhW3RoaXMucG9zKytdPDwxNil8dGhpcy5kYXRhW3RoaXMucG9zKytdPDwyNH0sdC5wcm90b3R5cGUucmVhZFVpbnQzMj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlYWRJbnQzMigpPj4+MH0sdC5wcm90b3R5cGUucmVhZFVpbnQxNj1mdW5jdGlvbigpe2lmKHRoaXMucG9zKzI+dGhpcy5kYXRhLmxlbmd0aCl0aHJvdyBuZXcgYSgiVW5leHBlY3RlZCBlbmQgb2YgZmlsZSIpO3JldHVybiB0aGlzLmRhdGFbdGhpcy5wb3MrK118dGhpcy5kYXRhW3RoaXMucG9zKytdPDw4fSx0LnByb3RvdHlwZS5yZWFkSW50MTY9ZnVuY3Rpb24oKXt2YXIgdD10aGlzLnJlYWRVaW50MTYoKTtyZXR1cm4gdD4zMjc2NyYmKHQtPTY1NTM2KSx0fSx0LnByb3RvdHlwZS5yZWFkU3RyaW5nPWZ1bmN0aW9uKHQpe2lmKHRoaXMucG9zK3Q+dGhpcy5kYXRhLmxlbmd0aCl0aHJvdyBuZXcgYSgiVW5leHBlY3RlZCBlbmQgb2YgZmlsZSIpO2Zvcih2YXIgZT0iIixpPTA7aTx0O2krKyllKz1TdHJpbmcuZnJvbUNoYXJDb2RlKHRoaXMuZGF0YVt0aGlzLnBvcysrXT4+PjApO3JldHVybiBlfSx0LnByb3RvdHlwZS5yZWFkTnVsbFRlcm1TdHJpbmc9ZnVuY3Rpb24odCl7dmFyIGU9IiI7aWYodD4wKXt0LS07Zm9yKHZhciBpPTA7aTx0O2krKyl7aWYodGhpcy5wb3MraSsxPnRoaXMuZGF0YS5sZW5ndGgpdGhyb3cgbmV3IGEoIlVuZXhwZWN0ZWQgZW5kIG9mIGZpbGUiKTt2YXIgcz10aGlzLmRhdGFbdGhpcy5wb3MraV0+Pj4wO2lmKDA9PT1zKWJyZWFrO2UrPVN0cmluZy5mcm9tQ2hhckNvZGUocyl9fXJldHVybiBlfSx0fSgpLHU9ZnVuY3Rpb24oKXtmdW5jdGlvbiB0KHQsZSxpKXtudWxsIT10Pyh0aGlzLng9dC5yZWFkSW50MTYoKSx0aGlzLnk9dC5yZWFkSW50MTYoKSk6KHRoaXMueD1lLHRoaXMueT1pKX1yZXR1cm4gdC5wcm90b3R5cGUuY2xvbmU9ZnVuY3Rpb24oKXtyZXR1cm4gbmV3IHQobnVsbCx0aGlzLngsdGhpcy55KX0sdC5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXtyZXR1cm4ie3g6ICIrdGhpcy54KyIsIHk6ICIrdGhpcy55KyJ9In0sdH0oKSxkPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUsaSxzLG4pe251bGwhPXQ/KHRoaXMuYm90dG9tPXQucmVhZEludDE2KCksdGhpcy5yaWdodD10LnJlYWRJbnQxNigpLHRoaXMudG9wPXQucmVhZEludDE2KCksdGhpcy5sZWZ0PXQucmVhZEludDE2KCkpOih0aGlzLmJvdHRvbT1uLHRoaXMucmlnaHQ9cyx0aGlzLnRvcD1pLHRoaXMubGVmdD1lKX1yZXR1cm4gdC5wcm90b3R5cGUuY2xvbmU9ZnVuY3Rpb24oKXtyZXR1cm4gbmV3IHQobnVsbCx0aGlzLmxlZnQsdGhpcy50b3AsdGhpcy5yaWdodCx0aGlzLmJvdHRvbSl9LHQucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIntsZWZ0OiAiK3RoaXMubGVmdCsiLCB0b3A6ICIrdGhpcy50b3ArIiwgcmlnaHQ6ICIrdGhpcy5yaWdodCsiLCBib3R0b206ICIrdGhpcy5ib3R0b20rIn0ifSx0LnByb3RvdHlwZS5lbXB0eT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmxlZnQ+PXRoaXMucmlnaHR8fHRoaXMudG9wPj10aGlzLmJvdHRvbX0sdC5wcm90b3R5cGUuaW50ZXJzZWN0PWZ1bmN0aW9uKGUpe3JldHVybiB0aGlzLmVtcHR5KCl8fGUuZW1wdHkoKXx8dGhpcy5sZWZ0Pj1lLnJpZ2h0fHx0aGlzLnRvcD49ZS5ib3R0b218fHRoaXMucmlnaHQ8PWUubGVmdHx8dGhpcy5ib3R0b208PWUudG9wP251bGw6bmV3IHQobnVsbCxNYXRoLm1heCh0aGlzLmxlZnQsZS5sZWZ0KSxNYXRoLm1heCh0aGlzLnRvcCxlLnRvcCksTWF0aC5taW4odGhpcy5yaWdodCxlLnJpZ2h0KSxNYXRoLm1pbih0aGlzLmJvdHRvbSxlLmJvdHRvbSkpfSx0fSgpLFQ9ZnVuY3Rpb24oKXtmdW5jdGlvbiB0KHQpe3RoaXMudHlwZT10fXJldHVybiB0LnByb3RvdHlwZS5jbG9uZT1mdW5jdGlvbigpe3Rocm93IG5ldyBhKCJjbG9uZSBub3QgaW1wbGVtZW50ZWQiKX0sdC5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXt0aHJvdyBuZXcgYSgidG9TdHJpbmcgbm90IGltcGxlbWVudGVkIil9LHR9KCksZj1mdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKGUsaSl7cmV0dXJuIHQ9T2JqZWN0LnNldFByb3RvdHlwZU9mfHx7X19wcm90b19fOltdfWluc3RhbmNlb2YgQXJyYXkmJmZ1bmN0aW9uKHQsZSl7dC5fX3Byb3RvX189ZX18fGZ1bmN0aW9uKHQsZSl7Zm9yKHZhciBpIGluIGUpT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGUsaSkmJih0W2ldPWVbaV0pfSx0KGUsaSl9O3JldHVybiBmdW5jdGlvbihlLGkpe2lmKCJmdW5jdGlvbiIhPXR5cGVvZiBpJiZudWxsIT09aSl0aHJvdyBuZXcgVHlwZUVycm9yKCJDbGFzcyBleHRlbmRzIHZhbHVlICIrU3RyaW5nKGkpKyIgaXMgbm90IGEgY29uc3RydWN0b3Igb3IgbnVsbCIpO2Z1bmN0aW9uIHMoKXt0aGlzLmNvbnN0cnVjdG9yPWV9dChlLGkpLGUucHJvdG90eXBlPW51bGw9PT1pP09iamVjdC5jcmVhdGUoaSk6KHMucHJvdG90eXBlPWkucHJvdG90eXBlLG5ldyBzKX19KCksRT1mdW5jdGlvbih0KXtmdW5jdGlvbiBlKGUsaSl7dmFyIHM9dC5jYWxsKHRoaXMsInJlZ2lvbiIpfHx0aGlzO2lmKG51bGwhPWUpe2lmKGUuc2tpcCgyKSw2IT09ZS5yZWFkSW50MTYoKSl0aHJvdyBuZXcgYSgiSW52YWxpZCByZWdpb24gaWRlbnRpZmllciIpO2Uuc2tpcCgyKSxlLnJlYWRJbnQxNigpO3ZhciBuPWUucmVhZEludDE2KCk7ZS5za2lwKDIpO3ZhciBvPWUucmVhZEludDE2KCkscj1lLnJlYWRJbnQxNigpLGg9ZS5yZWFkSW50MTYoKSxsPWUucmVhZEludDE2KCk7cy5ib3VuZHM9bmV3IGQobnVsbCxvLHIsaCxsKSxzLnNjYW5zPVtdO2Zvcih2YXIgYz0wO2M8bjtjKyspcy5zY2Fucy5wdXNoKG5ldyBnKGUpKTtzLl91cGRhdGVDb21wbGV4aXR5KCl9ZWxzZSBpZihudWxsIT1pKXtpZihzLmJvdW5kcz1udWxsIT1pLmJvdW5kcz9pLmJvdW5kcy5jbG9uZSgpOm51bGwsbnVsbCE9aS5zY2Fucylmb3Iocy5zY2Fucz1bXSxjPTA7YzxpLnNjYW5zLmxlbmd0aDtjKyspcy5zY2Fucy5wdXNoKGkuc2NhbnNbY10uY2xvbmUoKSk7ZWxzZSBzLnNjYW5zPW51bGw7cy5jb21wbGV4aXR5PWkuY29tcGxleGl0eX1lbHNlIHMuYm91bmRzPW51bGwscy5zY2Fucz1udWxsLHMuY29tcGxleGl0eT0wO3JldHVybiBzfXJldHVybiBmKGUsdCksZS5wcm90b3R5cGUuY2xvbmU9ZnVuY3Rpb24oKXtyZXR1cm4gbmV3IGUobnVsbCx0aGlzKX0sZS5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXtyZXR1cm4ie2NvbXBsZXhpdHk6ICIrWyJudWxsIiwic2ltcGxlIiwiY29tcGxleCJdW3RoaXMuY29tcGxleGl0eV0rIiBib3VuZHM6ICIrKG51bGwhPXRoaXMuYm91bmRzP3RoaXMuYm91bmRzLnRvU3RyaW5nKCk6Iltub25lXSIpKyIgI3NjYW5zOiAiKyhudWxsIT10aGlzLnNjYW5zP3RoaXMuc2NhbnMubGVuZ3RoOiJbbm9uZV0iKSsifSJ9LGUucHJvdG90eXBlLl91cGRhdGVDb21wbGV4aXR5PWZ1bmN0aW9uKCl7aWYobnVsbD09dGhpcy5ib3VuZHMpdGhpcy5jb21wbGV4aXR5PTAsdGhpcy5zY2Fucz1udWxsO2Vsc2UgaWYodGhpcy5ib3VuZHMuZW1wdHkoKSl0aGlzLmNvbXBsZXhpdHk9MCx0aGlzLnNjYW5zPW51bGwsdGhpcy5ib3VuZHM9bnVsbDtlbHNlIGlmKG51bGw9PXRoaXMuc2NhbnMpdGhpcy5jb21wbGV4aXR5PTE7ZWxzZSBpZih0aGlzLmNvbXBsZXhpdHk9MiwxPT09dGhpcy5zY2Fucy5sZW5ndGgpe3ZhciB0PXRoaXMuc2NhbnNbMF07aWYodC50b3A9PT10aGlzLmJvdW5kcy50b3AmJnQuYm90dG9tPT09dGhpcy5ib3VuZHMuYm90dG9tJiYxPT09dC5zY2FubGluZXMubGVuZ3RoKXt2YXIgZT10LnNjYW5saW5lc1swXTtlLmxlZnQ9PT10aGlzLmJvdW5kcy5sZWZ0JiZlLnJpZ2h0PT09dGhpcy5ib3VuZHMucmlnaHQmJih0aGlzLnNjYW5zPW51bGwsdGhpcy5jb21wbGV4aXR5PTEpfX19LGUucHJvdG90eXBlLnN1YnRyYWN0PWZ1bmN0aW9uKHQpe2lmKGMubG9nKCJbd21mXSBSZWdpb24gIit0aGlzLnRvU3RyaW5nKCkrIiBzdWJ0cmFjdCAiK3QudG9TdHJpbmcoKSksbnVsbCE9dGhpcy5ib3VuZHMmJm51bGwhPXRoaXMuYm91bmRzLmludGVyc2VjdCh0KSl7bnVsbD09dGhpcy5zY2FucyYmKHRoaXMuc2NhbnM9W10sdGhpcy5zY2Fucy5wdXNoKG5ldyBnKG51bGwsbnVsbCx0aGlzLmJvdW5kcy50b3AsdGhpcy5ib3VuZHMuYm90dG9tLFt7bGVmdDp0aGlzLmJvdW5kcy5sZWZ0LHJpZ2h0OnRoaXMuYm91bmRzLnJpZ2h0fV0pKSx0aGlzLmNvbXBsZXhpdHk9Mik7Zm9yKHZhciBlPTA7ZTx0aGlzLnNjYW5zLmxlbmd0aDspe2lmKCh1PXRoaXMuc2NhbnNbZV0pLmJvdHRvbT49dC50b3Ape3ZhciBpPXUuY2xvbmUoKTt1LmJvdHRvbT10LnRvcC0xLGkudG9wPXQudG9wLHUudG9wPj11LmJvdHRvbT90aGlzLnNjYW5zW2VdPWk6KGMubG9nKCJbd21mXSBSZWdpb24gc3BsaXQgdG9wIHNjYW4gIitlKyIgZm9yIHN1YnN0cmFjdGlvbiIpLHRoaXMuc2NhbnMuc3BsaWNlKCsrZSwwLGkpKTticmVha31lKyt9Zm9yKHZhciBzPWU7ZTx0aGlzLnNjYW5zLmxlbmd0aCYmISgodT10aGlzLnNjYW5zW2VdKS50b3A+dC5ib3R0b20pOyl7aWYodS5ib3R0b20+dC5ib3R0b20pe2k9dS5jbG9uZSgpLHUuYm90dG9tPXQuYm90dG9tLGkudG9wPXQuYm90dG9tKzEsdS50b3A+PXUuYm90dG9tP3RoaXMuc2NhbnNbZV09aTooYy5sb2coIlt3bWZdIFJlZ2lvbiBzcGxpdCBib3R0b20gc2NhbiAiK2UrIiBmb3Igc3Vic3RyYWN0aW9uIiksdGhpcy5zY2Fucy5zcGxpY2UoKytlLDAsaSkpO2JyZWFrfWUrK31pZihzPHRoaXMuc2NhbnMubGVuZ3RoKXt2YXIgbj1lO2ZvcihlPXM7ZTxuOykodT10aGlzLnNjYW5zW2VdKS5zdWJ0cmFjdCh0LmxlZnQsdC5yaWdodCk/ZSsrOihjLmxvZygiW3dtZl0gUmVnaW9uIHJlbW92ZSBub3cgZW1wdHkgc2NhbiAiK2UrIiBkdWUgdG8gc3VidHJhY3Rpb24iKSx0aGlzLnNjYW5zLnNwbGljZShlLDEpLG4tLSl9aWYobnVsbCE9dGhpcy5zY2Fucyl7Zm9yKHZhciBvLHI9dm9pZCAwLGE9dm9pZCAwLGg9dm9pZCAwLGw9dGhpcy5zY2Fucy5sZW5ndGgscD0wO3A8bDtwKyspe3ZhciB1PXRoaXMuc2NhbnNbcF07MD09PXAmJihvPXUudG9wKSxwPT09bC0xJiYoaD11LmJvdHRvbSk7dmFyIFQ9dS5zY2FubGluZXMubGVuZ3RoO2lmKFQ+MCl7dmFyIGY9dS5zY2FubGluZXNbMF07KG51bGw9PXJ8fGYubGVmdDxyKSYmKHI9Zi5sZWZ0KSxmPXUuc2NhbmxpbmVzW1QtMV0sKG51bGw9PWF8fGYucmlnaHQ+YSkmJihhPWYucmlnaHQpfX1udWxsIT1yJiZudWxsIT1vJiZudWxsIT1hJiZudWxsIT1oPyh0aGlzLmJvdW5kcz1uZXcgZChudWxsLHIsbyxhLGgpLHRoaXMuX3VwZGF0ZUNvbXBsZXhpdHkoKSk6KHRoaXMuYm91bmRzPW51bGwsdGhpcy5zY2Fucz1udWxsLHRoaXMuY29tcGxleGl0eT0wKX1lbHNlIHRoaXMuX3VwZGF0ZUNvbXBsZXhpdHkoKX1jLmxvZygiW3dtZl0gUmVnaW9uIHN1YnRyYWN0aW9uIC0+ICIrdGhpcy50b1N0cmluZygpKX0sZS5wcm90b3R5cGUuaW50ZXJzZWN0PWZ1bmN0aW9uKHQpe2lmKGMubG9nKCJbd21mXSBSZWdpb24gIit0aGlzLnRvU3RyaW5nKCkrIiBpbnRlcnNlY3Qgd2l0aCAiK3QudG9TdHJpbmcoKSksbnVsbCE9dGhpcy5ib3VuZHMpaWYodGhpcy5ib3VuZHM9dGhpcy5ib3VuZHMuaW50ZXJzZWN0KHQpLG51bGwhPXRoaXMuYm91bmRzKXtpZihudWxsIT10aGlzLnNjYW5zKXtmb3IodmFyIGU9MDtlPHRoaXMuc2NhbnMubGVuZ3RoJiYoaT10aGlzLnNjYW5zW2VdKS5ib3R0b208dGhpcy5ib3VuZHMudG9wOyllKys7Zm9yKGU+MCYmKGMubG9nKCJbd21mXSBSZWdpb24gcmVtb3ZlICIrZSsiIHNjYW5zIGZyb20gdG9wIiksdGhpcy5zY2Fucy5zcGxpY2UoMCxlKSx0aGlzLnNjYW5zLmxlbmd0aD4wJiYodGhpcy5zY2Fuc1swXS50b3A9dGhpcy5ib3VuZHMudG9wKSksZT0wO2U8dGhpcy5zY2Fucy5sZW5ndGg7KXt2YXIgaTtpZigoaT10aGlzLnNjYW5zW2VdKS50b3A+dGhpcy5ib3VuZHMuYm90dG9tKXtjLmxvZygiW3dtZl0gUmVnaW9uIHJlbW92ZSAiKyh0aGlzLnNjYW5zLmxlbmd0aC1lKSsiIHNjYW5zIGZyb20gYm90dG9tIiksdGhpcy5zY2Fucy5zcGxpY2UoZSx0aGlzLnNjYW5zLmxlbmd0aC1lKTticmVha31pLmludGVyc2VjdCh0aGlzLmJvdW5kcy5sZWZ0LHRoaXMuYm91bmRzLnJpZ2h0KT9lKys6KGMubG9nKCJbd21mXSBSZWdpb24gcmVtb3ZlIG5vdyBlbXB0eSBzY2FuICIrZSsiIGR1ZSB0byBpbnRlcnNlY3Rpb24iKSx0aGlzLnNjYW5zLnNwbGljZShlLDEpKX10aGlzLnNjYW5zLmxlbmd0aD4wJiYodGhpcy5zY2Fuc1t0aGlzLnNjYW5zLmxlbmd0aC0xXS5ib3R0b209dGhpcy5ib3VuZHMuYm90dG9tKSx0aGlzLl91cGRhdGVDb21wbGV4aXR5KCl9fWVsc2UgdGhpcy5zY2Fucz1udWxsLHRoaXMuY29tcGxleGl0eT0wO2MubG9nKCJbd21mXSBSZWdpb24gaW50ZXJzZWN0aW9uIC0+ICIrdGhpcy50b1N0cmluZygpKX0sZS5wcm90b3R5cGUub2Zmc2V0PWZ1bmN0aW9uKHQsZSl7aWYobnVsbCE9dGhpcy5ib3VuZHMmJih0aGlzLmJvdW5kcy5sZWZ0Kz10LHRoaXMuYm91bmRzLnRvcCs9ZSx0aGlzLmJvdW5kcy5yaWdodCs9dCx0aGlzLmJvdW5kcy5ib3R0b20rPWUpLG51bGwhPXRoaXMuc2NhbnMpZm9yKHZhciBpPXRoaXMuc2NhbnMubGVuZ3RoLHM9MDtzPGk7cysrKXt2YXIgbj10aGlzLnNjYW5zW3NdO24udG9wKz1lLG4uYm90dG9tKz1lO2Zvcih2YXIgbz1uLnNjYW5saW5lcy5sZW5ndGgscj0wO3I8bztyKyspe3ZhciBhPW4uc2NhbmxpbmVzW3JdO2EubGVmdCs9dCxhLnJpZ2h0Kz10fX19LGV9KFQpLGc9ZnVuY3Rpb24oKXtmdW5jdGlvbiB0KHQsZSxpLHMsbil7aWYobnVsbCE9dCl7dmFyIG89dC5yZWFkVWludDE2KCk7dGhpcy50b3A9dC5yZWFkVWludDE2KCksdGhpcy5ib3R0b209dC5yZWFkVWludDE2KCksdGhpcy5zY2FubGluZXM9W107Zm9yKHZhciByPTA7cjxvO3IrKyl7dmFyIGE9dC5yZWFkVWludDE2KCksaD10LnJlYWRVaW50MTYoKTt0aGlzLnNjYW5saW5lcy5wdXNoKHtsZWZ0OmEscmlnaHQ6aH0pfXQuc2tpcCgyKX1lbHNlIGlmKG51bGwhPWUpZm9yKHRoaXMudG9wPWUudG9wLHRoaXMuYm90dG9tPWUuYm90dG9tLHRoaXMuc2NhbmxpbmVzPVtdLHI9MDtyPGUuc2NhbmxpbmVzLmxlbmd0aDtyKyspe3ZhciBsPWUuc2NhbmxpbmVzW3JdO3RoaXMuc2NhbmxpbmVzLnB1c2goe2xlZnQ6bC5sZWZ0LHJpZ2h0OmwucmlnaHR9KX1lbHNlIHRoaXMudG9wPWksdGhpcy5ib3R0b209cyx0aGlzLnNjYW5saW5lcz1ufXJldHVybiB0LnByb3RvdHlwZS5jbG9uZT1mdW5jdGlvbigpe3JldHVybiBuZXcgdChudWxsLHRoaXMpfSx0LnByb3RvdHlwZS5zdWJ0cmFjdD1mdW5jdGlvbih0LGUpe3ZhciBpO2ZvcihpPTA7aTx0aGlzLnNjYW5saW5lcy5sZW5ndGgmJihvPXRoaXMuc2NhbmxpbmVzW2ldKS5sZWZ0PD10OylvLnJpZ2h0Pj10JiYoby5yaWdodD10LTEsby5sZWZ0Pj1vLnJpZ2h0KT90aGlzLnNjYW5saW5lcy5zcGxpY2UoaSwxKTppKys7Zm9yKHZhciBzPWksbj0wO2k8dGhpcy5zY2FubGluZXMubGVuZ3RoOyl7dmFyIG87aWYoKG89dGhpcy5zY2FubGluZXNbaV0pLnJpZ2h0PmUpe28ubGVmdD1lLG49aS1zLG8ubGVmdD49by5yaWdodCYmbisrO2JyZWFrfWkrK31yZXR1cm4gbj4wJiZzPHRoaXMuc2NhbmxpbmVzLmxlbmd0aCYmdGhpcy5zY2FubGluZXMuc3BsaWNlKHMsbiksdGhpcy5zY2FubGluZXMubGVuZ3RoPjB9LHQucHJvdG90eXBlLmludGVyc2VjdD1mdW5jdGlvbih0LGUpe2Zvcih2YXIgaT0wO2k8dGhpcy5zY2FubGluZXMubGVuZ3RoO2krKylpZigocz10aGlzLnNjYW5saW5lc1tpXSkubGVmdD49dHx8cy5yaWdodD49dCl7aT4wJiZ0aGlzLnNjYW5saW5lcy5zcGxpY2UoMCxpKTticmVha31pZih0aGlzLnNjYW5saW5lcy5sZW5ndGg+MCl7dmFyIHM7Zm9yKChzPXRoaXMuc2NhbmxpbmVzWzBdKS5sZWZ0PHQmJihzLmxlZnQ9dCksaT0wO2k8dGhpcy5zY2FubGluZXMubGVuZ3RoO2krKylpZigocz10aGlzLnNjYW5saW5lc1tpXSkubGVmdD5lKXt0aGlzLnNjYW5saW5lcy5zcGxpY2UoaSx0aGlzLnNjYW5saW5lcy5sZW5ndGgtaSk7YnJlYWt9dGhpcy5zY2FubGluZXMubGVuZ3RoPjAmJihzPXRoaXMuc2NhbmxpbmVzW3RoaXMuc2NhbmxpbmVzLmxlbmd0aC0xXSkucmlnaHQ+ZSYmKHMucmlnaHQ9ZSl9cmV0dXJuIHRoaXMuc2NhbmxpbmVzLmxlbmd0aD4wfSx0LnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiJ7ICNzY2FubGluZXM6ICIrdGhpcy5zY2FubGluZXMubGVuZ3RoKyJ9In0sdH0oKSxfPWZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24oZSxpKXtyZXR1cm4gdD1PYmplY3Quc2V0UHJvdG90eXBlT2Z8fHtfX3Byb3RvX186W119aW5zdGFuY2VvZiBBcnJheSYmZnVuY3Rpb24odCxlKXt0Ll9fcHJvdG9fXz1lfXx8ZnVuY3Rpb24odCxlKXtmb3IodmFyIGkgaW4gZSlPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoZSxpKSYmKHRbaV09ZVtpXSl9LHQoZSxpKX07cmV0dXJuIGZ1bmN0aW9uKGUsaSl7aWYoImZ1bmN0aW9uIiE9dHlwZW9mIGkmJm51bGwhPT1pKXRocm93IG5ldyBUeXBlRXJyb3IoIkNsYXNzIGV4dGVuZHMgdmFsdWUgIitTdHJpbmcoaSkrIiBpcyBub3QgYSBjb25zdHJ1Y3RvciBvciBudWxsIik7ZnVuY3Rpb24gcygpe3RoaXMuY29uc3RydWN0b3I9ZX10KGUsaSksZS5wcm90b3R5cGU9bnVsbD09PWk/T2JqZWN0LmNyZWF0ZShpKToocy5wcm90b3R5cGU9aS5wcm90b3R5cGUsbmV3IHMpfX0oKSx5PWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUpe2UmJnQuc2tpcCg0KSx0aGlzLndpZHRoPXQucmVhZFVpbnQxNigpLHRoaXMuaGVpZ2h0PXQucmVhZFVpbnQxNigpLHRoaXMucGxhbmVzPXQucmVhZFVpbnQxNigpLHRoaXMuYml0Y291bnQ9dC5yZWFkVWludDE2KCl9cmV0dXJuIHQucHJvdG90eXBlLmNvbG9ycz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJpdGNvdW50PD04PzE8PHRoaXMuYml0Y291bnQ6MH0sdH0oKSxTPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUpe2UmJnQuc2tpcCg0KSx0aGlzLndpZHRoPXQucmVhZEludDMyKCksdGhpcy5oZWlnaHQ9dC5yZWFkSW50MzIoKSx0aGlzLnBsYW5lcz10LnJlYWRVaW50MTYoKSx0aGlzLmJpdGNvdW50PXQucmVhZFVpbnQxNigpLHRoaXMuY29tcHJlc3Npb249dC5yZWFkVWludDMyKCksdGhpcy5zaXplaW1hZ2U9dC5yZWFkVWludDMyKCksdGhpcy54cGVsc3Blcm1ldGVyPXQucmVhZEludDMyKCksdGhpcy55cGVsc3Blcm1ldGVyPXQucmVhZEludDMyKCksdGhpcy5jbHJ1c2VkPXQucmVhZFVpbnQzMigpLHRoaXMuY2xyaW1wb3J0YW50PXQucmVhZFVpbnQzMigpfXJldHVybiB0LnByb3RvdHlwZS5jb2xvcnM9ZnVuY3Rpb24oKXtyZXR1cm4gMCE9PXRoaXMuY2xydXNlZD90aGlzLmNscnVzZWQ8MjU2P3RoaXMuY2xydXNlZDoyNTY6dGhpcy5iaXRjb3VudD44PzA6MTw8dGhpcy5iaXRjb3VudH0sdH0oKSxJPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUpe3RoaXMuX3JlYWRlcj10LHRoaXMuX29mZnNldD10LnBvcyx0aGlzLl91c2VyZ2I9ZTt2YXIgaT10LnJlYWRVaW50MzIoKTtpZih0aGlzLl9pbmZvc2l6ZT1pLGk9PT1jLkdESS5CSVRNQVBDT1JFSEVBREVSX1NJWkUpdGhpcy5faGVhZGVyPW5ldyB5KHQsITEpLHRoaXMuX2luZm9zaXplKz10aGlzLl9oZWFkZXIuY29sb3JzKCkqKGU/MzoyKTtlbHNle3RoaXMuX2hlYWRlcj1uZXcgUyh0LCExKTt2YXIgcz10aGlzLl9oZWFkZXIuY29tcHJlc3Npb249PT1jLkdESS5CaXRtYXBDb21wcmVzc2lvbi5CSV9CSVRGSUVMRFM/MzowO2k8PWMuR0RJLkJJVE1BUElORk9IRUFERVJfU0laRSs0KnMmJih0aGlzLl9pbmZvc2l6ZT1jLkdESS5CSVRNQVBJTkZPSEVBREVSX1NJWkUrNCpzKSx0aGlzLl9pbmZvc2l6ZSs9dGhpcy5faGVhZGVyLmNvbG9ycygpKihlPzQ6Mil9fXJldHVybiB0LnByb3RvdHlwZS5nZXRXaWR0aD1mdW5jdGlvbigpe3JldHVybiB0aGlzLl9oZWFkZXIud2lkdGh9LHQucHJvdG90eXBlLmdldEhlaWdodD1mdW5jdGlvbigpe3JldHVybiBNYXRoLmFicyh0aGlzLl9oZWFkZXIuaGVpZ2h0KX0sdC5wcm90b3R5cGUuaW5mb3NpemU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5faW5mb3NpemV9LHQucHJvdG90eXBlLmhlYWRlcj1mdW5jdGlvbigpe3JldHVybiB0aGlzLl9oZWFkZXJ9LHR9KCksdj1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCxlKXt0aGlzLl9yZWFkZXI9dCx0aGlzLl9vZmZzZXQ9dC5wb3MsdGhpcy5fc2l6ZT1lLHRoaXMuX2luZm89bmV3IEkodCwhMCl9cmV0dXJuIHQucHJvdG90eXBlLmdldFdpZHRoPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuX2luZm8uZ2V0V2lkdGgoKX0sdC5wcm90b3R5cGUuZ2V0SGVpZ2h0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuX2luZm8uZ2V0SGVpZ2h0KCl9LHQucHJvdG90eXBlLmJhc2U2NHJlZj1mdW5jdGlvbigpe3ZhciB0PXRoaXMuX3JlYWRlci5wb3M7dGhpcy5fcmVhZGVyLnNlZWsodGhpcy5fb2Zmc2V0KTt2YXIgZSxpPSJpbWFnZS9ibXAiLHM9dGhpcy5faW5mby5oZWFkZXIoKTtpZihzIGluc3RhbmNlb2YgUyYmbnVsbCE9cy5jb21wcmVzc2lvbilzd2l0Y2gocy5jb21wcmVzc2lvbil7Y2FzZSBjLkdESS5CaXRtYXBDb21wcmVzc2lvbi5CSV9KUEVHOmk9ImRhdGE6aW1hZ2UvanBlZyI7YnJlYWs7Y2FzZSBjLkdESS5CaXRtYXBDb21wcmVzc2lvbi5CSV9QTkc6aT0iZGF0YTppbWFnZS9wbmciO2JyZWFrO2RlZmF1bHQ6ZT10aGlzLm1ha2VCaXRtYXBGaWxlSGVhZGVyKCl9ZWxzZSBlPXRoaXMubWFrZUJpdG1hcEZpbGVIZWFkZXIoKTtudWxsIT1lP2UrPXRoaXMuX3JlYWRlci5yZWFkQmluYXJ5KHRoaXMuX3NpemUpOmU9dGhpcy5fcmVhZGVyLnJlYWRCaW5hcnkodGhpcy5fc2l6ZSk7dmFyIG49ImRhdGE6IitpKyI7YmFzZTY0LCIrYnRvYShlKTtyZXR1cm4gdGhpcy5fcmVhZGVyLnNlZWsodCksbn0sdC5wcm90b3R5cGUubWFrZUJpdG1hcEZpbGVIZWFkZXI9ZnVuY3Rpb24oKXt2YXIgdD1uZXcgQXJyYXlCdWZmZXIoMTQpLGU9bmV3IFVpbnQ4QXJyYXkodCk7cmV0dXJuIGVbMF09NjYsZVsxXT03NyxjLl93cml0ZVVpbnQzMlZhbChlLDIsdGhpcy5fc2l6ZSsxNCksYy5fd3JpdGVVaW50MzJWYWwoZSwxMCx0aGlzLl9pbmZvLmluZm9zaXplKCkrMTQpLGMuX2Jsb2JUb0JpbmFyeShlKX0sdH0oKSxBPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUpe2lmKG51bGwhPXQpe2lmKGU9ZSx0aGlzLl9yZWFkZXI9dCx0aGlzLl9vZmZzZXQ9dC5wb3MsdGhpcy5fc2l6ZT1lLHRoaXMudHlwZT10LnJlYWRJbnQxNigpLHRoaXMud2lkdGg9dC5yZWFkSW50MTYoKSx0aGlzLmhlaWdodD10LnJlYWRJbnQxNigpLHRoaXMud2lkdGhCeXRlcz10LnJlYWRJbnQxNigpLHRoaXMucGxhbmVzPXQucmVhZFVpbnQ4KCksdGhpcy5iaXRzUGl4ZWw9dC5yZWFkVWludDgoKSx0aGlzLmJpdHNPZmZzZXQ9dC5wb3MsdGhpcy5iaXRzU2l6ZT0odGhpcy53aWR0aCp0aGlzLmJpdHNQaXhlbCsxNT4+NDw8MSkqdGhpcy5oZWlnaHQsdGhpcy5iaXRzU2l6ZT5lLTEwKXRocm93IG5ldyBhKCJCaXRtYXAgc2hvdWxkIGhhdmUgIit0aGlzLmJpdHNTaXplKyIgYnl0ZXMsIGJ1dCBoYXMgIisoZS0xMCkpfWVsc2V7dmFyIGk9ZTt0aGlzLl9yZWFkZXI9aS5fcmVhZGVyLHRoaXMuX29mZnNldD1pLl9vZmZzZXQsdGhpcy5fc2l6ZT1pLl9zaXplLHRoaXMudHlwZT1pLnR5cGUsdGhpcy53aWR0aD1pLndpZHRoLHRoaXMuaGVpZ2h0PWkuaGVpZ2h0LHRoaXMud2lkdGhCeXRlcz1pLndpZHRoQnl0ZXMsdGhpcy5wbGFuZXM9aS5wbGFuZXMsdGhpcy5iaXRzUGl4ZWw9aS5iaXRzUGl4ZWwsdGhpcy5iaXRzT2Zmc2V0PWkuYml0c09mZnNldCx0aGlzLmJpdHNTaXplPWkuYml0c1NpemV9fXJldHVybiB0LnByb3RvdHlwZS5nZXRXaWR0aD1mdW5jdGlvbigpe3JldHVybiB0aGlzLndpZHRofSx0LnByb3RvdHlwZS5nZXRIZWlnaHQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5oZWlnaHR9LHQucHJvdG90eXBlLmNsb25lPWZ1bmN0aW9uKCl7cmV0dXJuIG5ldyB0KG51bGwsdGhpcyl9LHR9KCksYj1mdW5jdGlvbih0KXtmdW5jdGlvbiBlKGUsaSl7dmFyIHM9dC5jYWxsKHRoaXMsZSxpKXx8dGhpcztyZXR1cm4gbnVsbCE9ZSYmKHMuYml0c09mZnNldCs9MjIpLHN9cmV0dXJuIF8oZSx0KSxlLnByb3RvdHlwZS5jbG9uZT1mdW5jdGlvbigpe3JldHVybiBuZXcgZShudWxsLHRoaXMpfSxlfShBKSx3PWZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24oZSxpKXtyZXR1cm4gdD1PYmplY3Quc2V0UHJvdG90eXBlT2Z8fHtfX3Byb3RvX186W119aW5zdGFuY2VvZiBBcnJheSYmZnVuY3Rpb24odCxlKXt0Ll9fcHJvdG9fXz1lfXx8ZnVuY3Rpb24odCxlKXtmb3IodmFyIGkgaW4gZSlPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoZSxpKSYmKHRbaV09ZVtpXSl9LHQoZSxpKX07cmV0dXJuIGZ1bmN0aW9uKGUsaSl7aWYoImZ1bmN0aW9uIiE9dHlwZW9mIGkmJm51bGwhPT1pKXRocm93IG5ldyBUeXBlRXJyb3IoIkNsYXNzIGV4dGVuZHMgdmFsdWUgIitTdHJpbmcoaSkrIiBpcyBub3QgYSBjb25zdHJ1Y3RvciBvciBudWxsIik7ZnVuY3Rpb24gcygpe3RoaXMuY29uc3RydWN0b3I9ZX10KGUsaSksZS5wcm90b3R5cGU9bnVsbD09PWk/T2JqZWN0LmNyZWF0ZShpKToocy5wcm90b3R5cGU9aS5wcm90b3R5cGUsbmV3IHMpfX0oKSxSPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUsaSxzKXtudWxsIT10Pyh0aGlzLnI9dC5yZWFkVWludDgoKSx0aGlzLmc9dC5yZWFkVWludDgoKSx0aGlzLmI9dC5yZWFkVWludDgoKSx0LnNraXAoMSkpOih0aGlzLnI9ZSx0aGlzLmc9aSx0aGlzLmI9cyl9cmV0dXJuIHQucHJvdG90eXBlLmNsb25lPWZ1bmN0aW9uKCl7cmV0dXJuIG5ldyB0KG51bGwsdGhpcy5yLHRoaXMuZyx0aGlzLmIpfSx0LnByb3RvdHlwZS50b0hleD1mdW5jdGlvbigpe3JldHVybigxNjc3NzIxNisodGhpcy5yPDwxNnx0aGlzLmc8PDh8dGhpcy5iKSkudG9TdHJpbmcoMTYpLnNsaWNlKDEpfSx0LnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiJ7cjogIit0aGlzLnIrIiwgZzogIit0aGlzLmcrIiwgYjogIit0aGlzLmIrIn0ifSx0fSgpLE89ZnVuY3Rpb24odCl7ZnVuY3Rpb24gZShlLGkpe3ZhciBzPXQuY2FsbCh0aGlzLCJmb250Iil8fHRoaXM7aWYobnVsbCE9ZSl7cy5oZWlnaHQ9ZS5yZWFkSW50MTYoKSxzLndpZHRoPWUucmVhZEludDE2KCkscy5lc2NhcGVtZW50PWUucmVhZEludDE2KCkscy5vcmllbnRhdGlvbj1lLnJlYWRJbnQxNigpLHMud2VpZ2h0PWUucmVhZEludDE2KCkscy5pdGFsaWM9ZS5yZWFkVWludDgoKSxzLnVuZGVybGluZT1lLnJlYWRVaW50OCgpLHMuc3RyaWtlb3V0PWUucmVhZFVpbnQ4KCkscy5jaGFyc2V0PWUucmVhZFVpbnQ4KCkscy5vdXRwcmVjaXNpb249ZS5yZWFkVWludDgoKSxzLmNsaXBwcmVjaXNpb249ZS5yZWFkVWludDgoKSxzLnF1YWxpdHk9ZS5yZWFkVWludDgoKTt2YXIgbj1lLnJlYWRVaW50OCgpO3MucGl0Y2g9MTUmbixzLmZhbWlseT1uPj42JjM7dmFyIG89aSxyPWUucG9zO3MuZmFjZW5hbWU9ZS5yZWFkTnVsbFRlcm1TdHJpbmcoTWF0aC5taW4oby0oZS5wb3MtciksMzIpKX1lbHNlIG51bGwhPWk/KGk9aSxzLmhlaWdodD1pLmhlaWdodCxzLndpZHRoPWkud2lkdGgscy5lc2NhcGVtZW50PWkuZXNjYXBlbWVudCxzLm9yaWVudGF0aW9uPWkub3JpZW50YXRpb24scy53ZWlnaHQ9aS53ZWlnaHQscy5pdGFsaWM9aS5pdGFsaWMscy51bmRlcmxpbmU9aS51bmRlcmxpbmUscy5zdHJpa2VvdXQ9aS5zdHJpa2VvdXQscy5jaGFyc2V0PWkuY2hhcnNldCxzLm91dHByZWNpc2lvbj1pLm91dHByZWNpc2lvbixzLmNsaXBwcmVjaXNpb249aS5jbGlwcHJlY2lzaW9uLHMucXVhbGl0eT1pLnF1YWxpdHkscy5waXRjaD1pLnBpdGNoLHMuZmFtaWx5PWkuZmFtaWx5LHMuZmFjZW5hbWU9aS5mYWNlbmFtZSk6KHMuaGVpZ2h0PS04MCxzLndpZHRoPTAscy5lc2NhcGVtZW50PTAscy5vcmllbnRhdGlvbj0wLHMud2VpZ2h0PTQwMCxzLml0YWxpYz0wLHMudW5kZXJsaW5lPTAscy5zdHJpa2VvdXQ9MCxzLmNoYXJzZXQ9MCxzLm91dHByZWNpc2lvbj0wLHMuY2xpcHByZWNpc2lvbj0wLHMucXVhbGl0eT0wLHMucGl0Y2g9MCxzLmZhbWlseT0wLHMuZmFjZW5hbWU9IkhlbHZldGljYSIpO3JldHVybiBzfXJldHVybiB3KGUsdCksZS5wcm90b3R5cGUuY2xvbmU9ZnVuY3Rpb24oKXtyZXR1cm4gbmV3IGUobnVsbCx0aGlzKX0sZS5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXtyZXR1cm4gSlNPTi5zdHJpbmdpZnkodGhpcyl9LGV9KFQpLEQ9ZnVuY3Rpb24odCl7ZnVuY3Rpb24gZShlLGkscyl7dmFyIG49dC5jYWxsKHRoaXMsImJydXNoIil8fHRoaXM7aWYobnVsbCE9ZSl7dmFyIG89aSxyPWUucG9zO2lmKCEwPT09c3x8ITE9PT1zKXN3aXRjaChuLnN0eWxlPWUucmVhZFVpbnQxNigpLHMmJm4uc3R5bGUhPT1jLkdESS5CcnVzaFN0eWxlLkJTX1BBVFRFUk4mJihuLnN0eWxlPWMuR0RJLkJydXNoU3R5bGUuQlNfRElCUEFUVEVSTlBUKSxuLnN0eWxlKXtjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfU09MSUQ6bi5jb2xvcj1uZXcgUihlKTticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfUEFUVEVSTjplLnNraXAocz8yOjYpLG4ucGF0dGVybj1uZXcgQShlLG8tKGUucG9zLXIpKTticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfRElCUEFUVEVSTlBUOm4uY29sb3J1c2FnZT1zP2UucmVhZFVpbnQxNigpOmUucmVhZFVpbnQzMigpLHN8fGUuc2tpcCgyKSxuLmRpYnBhdHRlcm5wdD1uZXcgdihlLG8tKGUucG9zLXIpKTticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfSEFUQ0hFRDpuLmNvbG9yPW5ldyBSKGUpLG4uaGF0Y2hzdHlsZT1lLnJlYWRVaW50MTYoKX1lbHNlIHMgaW5zdGFuY2VvZiBiJiYobi5zdHlsZT1jLkdESS5CcnVzaFN0eWxlLkJTX1BBVFRFUk4sbi5wYXR0ZXJuPXMpfWVsc2UgaWYobnVsbCE9aSlzd2l0Y2goaT1pLG4uc3R5bGU9aS5zdHlsZSxuLnN0eWxlKXtjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfU09MSUQ6bi5jb2xvcj1pLmNvbG9yLmNsb25lKCk7YnJlYWs7Y2FzZSBjLkdESS5CcnVzaFN0eWxlLkJTX1BBVFRFUk46bi5wYXR0ZXJuPWkucGF0dGVybi5jbG9uZSgpO2JyZWFrO2Nhc2UgYy5HREkuQnJ1c2hTdHlsZS5CU19ESUJQQVRURVJOUFQ6bi5jb2xvcnVzYWdlPWkuY29sb3J1c2FnZSxuLmRpYnBhdHRlcm5wdD1pLmRpYnBhdHRlcm5wdDticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfSEFUQ0hFRDpuLmNvbG9yPWkuY29sb3IuY2xvbmUoKSxuLmhhdGNoc3R5bGU9aS5oYXRjaHN0eWxlfXJldHVybiBufXJldHVybiB3KGUsdCksZS5wcm90b3R5cGUuY2xvbmU9ZnVuY3Rpb24oKXtyZXR1cm4gbmV3IGUobnVsbCx0aGlzKX0sZS5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXt2YXIgdD0ie3N0eWxlOiAiK3RoaXMuc3R5bGU7c3dpdGNoKHRoaXMuc3R5bGUpe2Nhc2UgYy5HREkuQnJ1c2hTdHlsZS5CU19TT0xJRDp0Kz0iLCBjb2xvcjogIit0aGlzLmNvbG9yLnRvU3RyaW5nKCk7YnJlYWs7Y2FzZSBjLkdESS5CcnVzaFN0eWxlLkJTX0RJQlBBVFRFUk5QVDp0Kz0iLCBjb2xvcnVzYWdlOiAiK3RoaXMuY29sb3J1c2FnZTticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfSEFUQ0hFRDp0Kz0iLCBjb2xvcjogIit0aGlzLmNvbG9yLnRvU3RyaW5nKCkrIiwgaGF0Y2hzdHlsZTogIit0aGlzLmhhdGNoc3R5bGV9cmV0dXJuIHQrIn0ifSxlfShUKSxNPWZ1bmN0aW9uKHQpe2Z1bmN0aW9uIGUoZSxpLHMsbixvLHIpe3ZhciBhPXQuY2FsbCh0aGlzLCJwZW4iKXx8dGhpcztyZXR1cm4gbnVsbCE9ZT8oaT1lLnJlYWRVaW50MTYoKSxhLnN0eWxlPTI1NSZpLGEud2lkdGg9bmV3IHUoZSksYS5jb2xvcj1uZXcgUihlKSxhLmxpbmVjYXA9aSYoYy5HREkuUGVuU3R5bGUuUFNfRU5EQ0FQX1NRVUFSRXxjLkdESS5QZW5TdHlsZS5QU19FTkRDQVBfRkxBVCksYS5qb2luPWkmKGMuR0RJLlBlblN0eWxlLlBTX0pPSU5fQkVWRUx8Yy5HREkuUGVuU3R5bGUuUFNfSk9JTl9NSVRFUikpOihhLnN0eWxlPWksYS53aWR0aD1zLGEuY29sb3I9bixhLmxpbmVjYXA9byxhLmpvaW49ciksYX1yZXR1cm4gdyhlLHQpLGUucHJvdG90eXBlLmNsb25lPWZ1bmN0aW9uKCl7cmV0dXJuIG5ldyBlKG51bGwsdGhpcy5zdHlsZSx0aGlzLndpZHRoLmNsb25lKCksdGhpcy5jb2xvci5jbG9uZSgpLHRoaXMubGluZWNhcCx0aGlzLmpvaW4pfSxlLnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiJ7c3R5bGU6ICIrdGhpcy5zdHlsZSsiLCB3aWR0aDogIit0aGlzLndpZHRoLnRvU3RyaW5nKCkrIiwgY29sb3I6ICIrdGhpcy5jb2xvci50b1N0cmluZygpKyIsIGxpbmVjYXA6ICIrdGhpcy5saW5lY2FwKyIsIGpvaW46ICIrdGhpcy5qb2luKyJ9In0sZX0oVCksUD1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCxlKXtudWxsIT10Pyh0aGlzLmZsYWc9dC5yZWFkVWludDgoKSx0aGlzLmI9dC5yZWFkVWludDgoKSx0aGlzLmc9dC5yZWFkVWludDgoKSx0aGlzLnI9dC5yZWFkVWludDgoKSk6KHRoaXMuZmxhZz1lLmZsYWcsdGhpcy5iPWUuYix0aGlzLmc9ZS5nLHRoaXMucj1lLnIpfXJldHVybiB0LnByb3RvdHlwZS5jbG9uZT1mdW5jdGlvbigpe3JldHVybiBuZXcgdChudWxsLHRoaXMpfSx0fSgpLG09ZnVuY3Rpb24odCl7ZnVuY3Rpb24gZShlLGkpe3ZhciBzPXQuY2FsbCh0aGlzLCJwYWxldHRlIil8fHRoaXM7aWYobnVsbCE9ZSl7cy5zdGFydD1lLnJlYWRVaW50MTYoKTt2YXIgbj1lLnJlYWRVaW50MTYoKTtmb3Iocy5lbnRyaWVzPVtdO24+MDspcy5lbnRyaWVzLnB1c2gobmV3IFAoZSkpLG4tLX1lbHNle3Muc3RhcnQ9aS5zdGFydCxzLmVudHJpZXM9W107Zm9yKHZhciBvPWkuZW50cmllcy5sZW5ndGgscj0wO3I8bztyKyspcy5lbnRyaWVzLnB1c2goaS5lbnRyaWVzW3JdKX1yZXR1cm4gc31yZXR1cm4gdyhlLHQpLGUucHJvdG90eXBlLmNsb25lPWZ1bmN0aW9uKCl7cmV0dXJuIG5ldyBlKG51bGwsdGhpcyl9LGUucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuInsgI2VudHJpZXM6ICIrdGhpcy5lbnRyaWVzLmxlbmd0aCsifSJ9LGV9KFQpLEM9ZnVuY3Rpb24odCxlKXtpZihudWxsIT10KWZvcih2YXIgaSBpbiB0aGlzLl9zdmdncm91cD10Ll9zdmdncm91cCx0aGlzLl9zdmdjbGlwQ2hhbmdlZD10Ll9zdmdjbGlwQ2hhbmdlZCx0aGlzLl9zdmd0ZXh0YmtmaWx0ZXI9dC5fc3ZndGV4dGJrZmlsdGVyLHRoaXMubWFwbW9kZT10Lm1hcG1vZGUsdGhpcy5zdHJldGNobW9kZT10LnN0cmV0Y2htb2RlLHRoaXMudGV4dGFsaWduPXQudGV4dGFsaWduLHRoaXMuYmttb2RlPXQuYmttb2RlLHRoaXMudGV4dGNvbG9yPXQudGV4dGNvbG9yLmNsb25lKCksdGhpcy5ia2NvbG9yPXQuYmtjb2xvci5jbG9uZSgpLHRoaXMucG9seWZpbGxtb2RlPXQucG9seWZpbGxtb2RlLHRoaXMud3g9dC53eCx0aGlzLnd5PXQud3ksdGhpcy53dz10Lnd3LHRoaXMud2g9dC53aCx0aGlzLnZ4PXQudngsdGhpcy52eT10LnZ5LHRoaXMudnc9dC52dyx0aGlzLnZoPXQudmgsdGhpcy54PXQueCx0aGlzLnk9dC55LHRoaXMuY2xpcD10LmNsaXAsdGhpcy5vd25jbGlwPSExLHRoaXMuc2VsZWN0ZWQ9e30sdC5zZWxlY3RlZCl0aGlzLnNlbGVjdGVkW2ldPXQuc2VsZWN0ZWRbaV07ZWxzZSBmb3IodmFyIGkgaW4gdGhpcy5fc3ZnZ3JvdXA9bnVsbCx0aGlzLl9zdmdjbGlwQ2hhbmdlZD0hMSx0aGlzLl9zdmd0ZXh0YmtmaWx0ZXI9bnVsbCx0aGlzLm1hcG1vZGU9Yy5HREkuTWFwTW9kZS5NTV9BTklTT1RST1BJQyx0aGlzLnN0cmV0Y2htb2RlPWMuR0RJLlN0cmV0Y2hNb2RlLkNPTE9ST05DT0xPUix0aGlzLnRleHRhbGlnbj0wLHRoaXMuYmttb2RlPWMuR0RJLk1peE1vZGUuT1BBUVVFLHRoaXMudGV4dGNvbG9yPW5ldyBSKG51bGwsMCwwLDApLHRoaXMuYmtjb2xvcj1uZXcgUihudWxsLDI1NSwyNTUsMjU1KSx0aGlzLnBvbHlmaWxsbW9kZT1jLkdESS5Qb2x5RmlsbE1vZGUuQUxURVJOQVRFLHRoaXMud3g9MCx0aGlzLnd5PTAsdGhpcy53dz0wLHRoaXMud2g9MCx0aGlzLnZ4PTAsdGhpcy52eT0wLHRoaXMudnc9MCx0aGlzLnZoPTAsdGhpcy54PTAsdGhpcy55PTAsdGhpcy5jbGlwPW51bGwsdGhpcy5vd25jbGlwPSExLHRoaXMuc2VsZWN0ZWQ9e30sZSl7dmFyIHM9ZVtpXTt0aGlzLnNlbGVjdGVkW2ldPW51bGwhPXM/cy5jbG9uZSgpOm51bGx9fSxOPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0KXt0aGlzLl9zdmc9dCx0aGlzLl9zdmdkZWZzPW51bGwsdGhpcy5fc3ZnUGF0dGVybnM9e30sdGhpcy5fc3ZnQ2xpcFBhdGhzPXt9LHRoaXMuZGVmT2JqZWN0cz17YnJ1c2g6bmV3IEQobnVsbCxudWxsKSxwZW46bmV3IE0obnVsbCxjLkdESS5QZW5TdHlsZS5QU19TT0xJRCxuZXcgdShudWxsLDEsMSksbmV3IFIobnVsbCwwLDAsMCksMCwwKSxmb250Om5ldyBPKG51bGwsbnVsbCkscGFsZXR0ZTpudWxsLHJlZ2lvbjpudWxsfSx0aGlzLnN0YXRlPW5ldyBDKG51bGwsdGhpcy5kZWZPYmplY3RzKSx0aGlzLnN0YXRlc3RhY2s9W3RoaXMuc3RhdGVdLHRoaXMub2JqZWN0cz17fX1yZXR1cm4gdC5wcm90b3R5cGUuc2V0TWFwTW9kZT1mdW5jdGlvbih0KXtjLmxvZygiW2dkaV0gc2V0TWFwTW9kZTogbW9kZT0iK3QpLHRoaXMuc3RhdGUubWFwbW9kZT10LHRoaXMuc3RhdGUuX3N2Z2dyb3VwPW51bGx9LHQucHJvdG90eXBlLnNldFdpbmRvd09yZz1mdW5jdGlvbih0LGUpe2MubG9nKCJbZ2RpXSBzZXRXaW5kb3dPcmc6IHg9Iit0KyIgeT0iK2UpLHRoaXMuc3RhdGUud3g9dCx0aGlzLnN0YXRlLnd5PWUsdGhpcy5zdGF0ZS5fc3ZnZ3JvdXA9bnVsbH0sdC5wcm90b3R5cGUuc2V0V2luZG93RXh0PWZ1bmN0aW9uKHQsZSl7Yy5sb2coIltnZGldIHNldFdpbmRvd0V4dDogeD0iK3QrIiB5PSIrZSksdGhpcy5zdGF0ZS53dz10LHRoaXMuc3RhdGUud2g9ZSx0aGlzLnN0YXRlLl9zdmdncm91cD1udWxsfSx0LnByb3RvdHlwZS5vZmZzZXRXaW5kb3dPcmc9ZnVuY3Rpb24odCxlKXtjLmxvZygiW2dkaV0gb2Zmc2V0V2luZG93T3JnOiBvZmZYPSIrdCsiIG9mZlk9IitlKSx0aGlzLnN0YXRlLnd4Kz10LHRoaXMuc3RhdGUud3krPWUsdGhpcy5zdGF0ZS5fc3ZnZ3JvdXA9bnVsbH0sdC5wcm90b3R5cGUuc2V0Vmlld3BvcnRPcmc9ZnVuY3Rpb24odCxlKXtjLmxvZygiW2dkaV0gc2V0Vmlld3BvcnRPcmc6IHg9Iit0KyIgeT0iK2UpLHRoaXMuc3RhdGUudng9dCx0aGlzLnN0YXRlLnZ5PWUsdGhpcy5zdGF0ZS5fc3ZnZ3JvdXA9bnVsbH0sdC5wcm90b3R5cGUuc2V0Vmlld3BvcnRFeHQ9ZnVuY3Rpb24odCxlKXtjLmxvZygiW2dkaV0gc2V0Vmlld3BvcnRFeHQ6IHg9Iit0KyIgeT0iK2UpLHRoaXMuc3RhdGUudnc9dCx0aGlzLnN0YXRlLnZoPWUsdGhpcy5zdGF0ZS5fc3ZnZ3JvdXA9bnVsbH0sdC5wcm90b3R5cGUub2Zmc2V0Vmlld3BvcnRPcmc9ZnVuY3Rpb24odCxlKXtjLmxvZygiW2dkaV0gb2Zmc2V0Vmlld3BvcnRPcmc6IG9mZlg9Iit0KyIgb2ZmWT0iK2UpLHRoaXMuc3RhdGUudngrPXQsdGhpcy5zdGF0ZS52eSs9ZSx0aGlzLnN0YXRlLl9zdmdncm91cD1udWxsfSx0LnByb3RvdHlwZS5zYXZlREM9ZnVuY3Rpb24oKXtjLmxvZygiW2dkaV0gc2F2ZURDIik7dmFyIHQ9dGhpcy5zdGF0ZTt0aGlzLnN0YXRlPW5ldyBDKHRoaXMuc3RhdGUpLHRoaXMuc3RhdGVzdGFjay5wdXNoKHQpLHRoaXMuc3RhdGUuX3N2Z2dyb3VwPW51bGx9LHQucHJvdG90eXBlLnJlc3RvcmVEQz1mdW5jdGlvbih0KXtpZihjLmxvZygiW2dkaV0gcmVzdG9yZURDOiBzYXZlZD0iK3QpLCEodGhpcy5zdGF0ZXN0YWNrLmxlbmd0aD4xKSl0aHJvdyBuZXcgYSgiTm8gc2F2ZWQgY29udGV4dHMiKTtpZigtMT09PXQpdGhpcy5zdGF0ZT10aGlzLnN0YXRlc3RhY2sucG9wKCk7ZWxzZXtpZih0PC0xKXRocm93IG5ldyBhKCJyZXN0b3JlREM6IHJlbGF0aXZlIHJlc3RvcmUgbm90IGltcGxlbWVudGVkIik7aWYodD4xKXRocm93IG5ldyBhKCJyZXN0b3JlREM6IGFic29sdXRlIHJlc3RvcmUgbm90IGltcGxlbWVudGVkIil9dGhpcy5zdGF0ZS5fc3ZnZ3JvdXA9bnVsbH0sdC5wcm90b3R5cGUuZXNjYXBlPWZ1bmN0aW9uKHQsZSxpLHMpe2MubG9nKCJbZ2RpXSBlc2NhcGU6IGZ1bmM9Iit0KyIgb2Zmc2V0PSIraSsiIGNvdW50PSIrcyl9LHQucHJvdG90eXBlLnNldFN0cmV0Y2hCbHRNb2RlPWZ1bmN0aW9uKHQpe2MubG9nKCJbZ2RpXSBzZXRTdHJldGNoQmx0TW9kZTogc3RyZXRjaE1vZGU9Iit0KX0sdC5wcm90b3R5cGUuc3RyZXRjaERpYj1mdW5jdGlvbih0LGUsaSxzLG4sbyxyLGEsaCxsLHApe2MubG9nKCJbZ2RpXSBzdHJldGNoRGliOiBzcmNYPSIrdCsiIHNyY1k9IitlKyIgc3JjVz0iK2krIiBzcmNIPSIrcysiIGRzdFg9IituKyIgZHN0WT0iK28rIiBkc3RXPSIrcisiIGRzdEg9IithKyIgcmFzdGVyT3A9MHgiK2gudG9TdHJpbmcoMTYpKSx0PXRoaXMuX3RvZGV2WCh0KSxlPXRoaXMuX3RvZGV2WShlKSxpPXRoaXMuX3RvZGV2VyhpKSxzPXRoaXMuX3RvZGV2SChzKSxuPXRoaXMuX3RvZGV2WChuKSxvPXRoaXMuX3RvZGV2WShvKSxyPXRoaXMuX3RvZGV2VyhyKSxhPXRoaXMuX3RvZGV2SChhKSxjLmxvZygiW2dkaV0gc3RyZXRjaERpYjogVFJBTlNMQVRFRDogc3JjWD0iK3QrIiBzcmNZPSIrZSsiIHNyY1c9IitpKyIgc3JjSD0iK3MrIiBkc3RYPSIrbisiIGRzdFk9IitvKyIgZHN0Vz0iK3IrIiBkc3RIPSIrYSsiIHJhc3Rlck9wPTB4IitoLnRvU3RyaW5nKDE2KSsiIGNvbG9yVXNhZ2U9MHgiK2wudG9TdHJpbmcoMTYpKSx0aGlzLl9wdXNoR3JvdXAoKSx0aGlzLl9zdmcuaW1hZ2UodGhpcy5zdGF0ZS5fc3ZnZ3JvdXAsbixvLHIsYSxwLmJhc2U2NHJlZigpKX0sdC5wcm90b3R5cGUuZGliQml0cz1mdW5jdGlvbih0LGUsaSxzLG4sbyxyLGEpe2MubG9nKCJbZ2RpXSBzdHJldGNoRGliQml0czogc3JjWD0iK3QrIiBzcmNZPSIrZSsiIGRzdFg9IitpKyIgZHN0WT0iK3MrIiB3aWR0aD0iK24rIiBoZWlnaHQ9IitvKyIgcmFzdGVyT3A9MHgiK3IudG9TdHJpbmcoMTYpKSx0PXRoaXMuX3RvZGV2WCh0KSxlPXRoaXMuX3RvZGV2WShlKSxpPXRoaXMuX3RvZGV2WChpKSxzPXRoaXMuX3RvZGV2WShzKSxuPXRoaXMuX3RvZGV2VyhuKSxvPXRoaXMuX3RvZGV2SChvKSxjLmxvZygiW2dkaV0gZGliQml0czogVFJBTlNMQVRFRDogc3JjWD0iK3QrIiBzcmNZPSIrZStOYU4raSsiIGRzdFk9IitzKyIgd2lkdGg9IituKyIgaGVpZ2h0PSIrbysiIHJhc3Rlck9wPTB4IityLnRvU3RyaW5nKDE2KSksdGhpcy5fcHVzaEdyb3VwKCksdGhpcy5fc3ZnLmltYWdlKHRoaXMuc3RhdGUuX3N2Z2dyb3VwLGkscyxuLG8sYS5iYXNlNjRyZWYoKSl9LHQucHJvdG90eXBlLnN0cmV0Y2hEaWJCaXRzPWZ1bmN0aW9uKHQsZSxpLHMsbixvLHIsYSxoLGwpe2MubG9nKCJbZ2RpXSBzdHJldGNoRGliQml0czogc3JjWD0iK3QrIiBzcmNZPSIrZSsiIHNyY1c9IitpKyIgc3JjSD0iK3MrIiBkc3RYPSIrbisiIGRzdFk9IitvKyIgZHN0Vz0iK3IrIiBkc3RIPSIrYSsiIHJhc3Rlck9wPTB4IitoLnRvU3RyaW5nKDE2KSksdD10aGlzLl90b2RldlgodCksZT10aGlzLl90b2RldlkoZSksaT10aGlzLl90b2RldlcoaSkscz10aGlzLl90b2Rldkgocyksbj10aGlzLl90b2Rldlgobiksbz10aGlzLl90b2Rldlkobykscj10aGlzLl90b2RldlcociksYT10aGlzLl90b2RldkgoYSksYy5sb2coIltnZGldIHN0cmV0Y2hEaWJCaXRzOiBUUkFOU0xBVEVEOiBzcmNYPSIrdCsiIHNyY1k9IitlKyIgc3JjVz0iK2krIiBzcmNIPSIrcysiIGRzdFg9IituKyIgZHN0WT0iK28rIiBkc3RXPSIrcisiIGRzdEg9IithKyIgcmFzdGVyT3A9MHgiK2gudG9TdHJpbmcoMTYpKSx0aGlzLl9wdXNoR3JvdXAoKSx0aGlzLl9zdmcuaW1hZ2UodGhpcy5zdGF0ZS5fc3ZnZ3JvdXAsbixvLHIsYSxsLmJhc2U2NHJlZigpKX0sdC5wcm90b3R5cGUucmVjdGFuZ2xlPWZ1bmN0aW9uKHQsZSxpKXtjLmxvZygiW2dkaV0gcmVjdGFuZ2xlOiByZWN0PSIrdC50b1N0cmluZygpKyIgd2l0aCBwZW4gIit0aGlzLnN0YXRlLnNlbGVjdGVkLnBlbi50b1N0cmluZygpKyIgYW5kIGJydXNoICIrdGhpcy5zdGF0ZS5zZWxlY3RlZC5icnVzaC50b1N0cmluZygpKTt2YXIgcz10aGlzLl90b2RldlkodC5ib3R0b20pLG49dGhpcy5fdG9kZXZYKHQucmlnaHQpLG89dGhpcy5fdG9kZXZZKHQudG9wKSxyPXRoaXMuX3RvZGV2WCh0LmxlZnQpO2U9dGhpcy5fdG9kZXZIKGUpLGk9dGhpcy5fdG9kZXZIKGkpLGMubG9nKCJbZ2RpXSByZWN0YW5nbGU6IFRSQU5TTEFURUQ6IGJvdHRvbT0iK3MrIiByaWdodD0iK24rIiB0b3A9IitvKyIgbGVmdD0iK3IrIiByaD0iK2krIiBydz0iK2UpLHRoaXMuX3B1c2hHcm91cCgpO3ZhciBhPXRoaXMuX2FwcGx5T3B0cyhudWxsLCEwLCEwLCExKTt0aGlzLl9zdmcucmVjdCh0aGlzLnN0YXRlLl9zdmdncm91cCxyLG8sbi1yLHMtbyxlLzIsaS8yLGEpfSx0LnByb3RvdHlwZS50ZXh0T3V0PWZ1bmN0aW9uKHQsZSxpKXtjLmxvZygiW2dkaV0gdGV4dE91dDogeD0iK3QrIiB5PSIrZSsiIHRleHQ9IitpKyIgd2l0aCBmb250ICIrdGhpcy5zdGF0ZS5zZWxlY3RlZC5mb250LnRvU3RyaW5nKCkpLHQ9dGhpcy5fdG9kZXZYKHQpLGU9dGhpcy5fdG9kZXZZKGUpLGMubG9nKCJbZ2RpXSB0ZXh0T3V0OiBUUkFOU0xBVEVEOiB4PSIrdCsiIHk9IitlKSx0aGlzLl9wdXNoR3JvdXAoKTt2YXIgcz10aGlzLl9hcHBseU9wdHMobnVsbCwhMSwhMSwhMCk7aWYoMCE9PXRoaXMuc3RhdGUuc2VsZWN0ZWQuZm9udC5lc2NhcGVtZW50JiYocy50cmFuc2Zvcm09InJvdGF0ZSgiK1stdGhpcy5zdGF0ZS5zZWxlY3RlZC5mb250LmVzY2FwZW1lbnQvMTAsdCxlXSsiKSIscy5zdHlsZT0iZG9taW5hbnQtYmFzZWxpbmU6IG1pZGRsZTsgdGV4dC1hbmNob3I6IHN0YXJ0OyIpLHRoaXMuc3RhdGUuYmttb2RlPT09Yy5HREkuTWl4TW9kZS5PUEFRVUUpe2lmKG51bGw9PXRoaXMuc3RhdGUuX3N2Z3RleHRia2ZpbHRlcil7dmFyIG49Yy5fbWFrZVVuaXF1ZUlkKCJmIiksbz10aGlzLl9zdmcuZmlsdGVyKHRoaXMuX2dldFN2Z0RlZigpLG4sMCwwLDEsMSk7dGhpcy5fc3ZnLmZpbHRlcnMuZmxvb2QobyxudWxsLCIjIit0aGlzLnN0YXRlLmJrY29sb3IudG9IZXgoKSwxKSx0aGlzLl9zdmcuZmlsdGVycy5jb21wb3NpdGUobyxudWxsLG51bGwsIlNvdXJjZUdyYXBoaWMiKSx0aGlzLnN0YXRlLl9zdmd0ZXh0YmtmaWx0ZXI9b31zLmZpbHRlcj0idXJsKCMiK3RoaXMuc3RhdGUuX3N2Z3RleHRia2ZpbHRlci5pZCsiKSJ9dGhpcy5fc3ZnLnRleHQodGhpcy5zdGF0ZS5fc3ZnZ3JvdXAsdCxlLGkscyl9LHQucHJvdG90eXBlLmV4dFRleHRPdXQ9ZnVuY3Rpb24odCxlLGkscyxuLG8pe2MubG9nKCJbZ2RpXSBleHRUZXh0T3V0OiB4PSIrdCsiIHk9IitlKyIgdGV4dD0iK2krIiB3aXRoIGZvbnQgIit0aGlzLnN0YXRlLnNlbGVjdGVkLmZvbnQudG9TdHJpbmcoKSksdD10aGlzLl90b2RldlgodCksZT10aGlzLl90b2RldlkoZSksYy5sb2coIltnZGldIGV4dFRleHRPdXQ6IFRSQU5TTEFURUQ6IHg9Iit0KyIgeT0iK2UpLHRoaXMuX3B1c2hHcm91cCgpO3ZhciByPXRoaXMuX2FwcGx5T3B0cyhudWxsLCExLCExLCEwKTtpZigwIT09dGhpcy5zdGF0ZS5zZWxlY3RlZC5mb250LmVzY2FwZW1lbnQmJihyLnRyYW5zZm9ybT0icm90YXRlKCIrWy10aGlzLnN0YXRlLnNlbGVjdGVkLmZvbnQuZXNjYXBlbWVudC8xMCx0LGVdKyIpIixyLnN0eWxlPSJkb21pbmFudC1iYXNlbGluZTogbWlkZGxlOyB0ZXh0LWFuY2hvcjogc3RhcnQ7IiksdGhpcy5zdGF0ZS5ia21vZGU9PT1jLkdESS5NaXhNb2RlLk9QQVFVRSl7aWYobnVsbD09dGhpcy5zdGF0ZS5fc3ZndGV4dGJrZmlsdGVyKXt2YXIgYT1jLl9tYWtlVW5pcXVlSWQoImYiKSxoPXRoaXMuX3N2Zy5maWx0ZXIodGhpcy5fZ2V0U3ZnRGVmKCksYSwwLDAsMSwxKTt0aGlzLl9zdmcuZmlsdGVycy5mbG9vZChoLG51bGwsIiMiK3RoaXMuc3RhdGUuYmtjb2xvci50b0hleCgpLDEpLHRoaXMuX3N2Zy5maWx0ZXJzLmNvbXBvc2l0ZShoLG51bGwsbnVsbCwiU291cmNlR3JhcGhpYyIpLHRoaXMuc3RhdGUuX3N2Z3RleHRia2ZpbHRlcj1ofXIuZmlsdGVyPSJ1cmwoIyIrdGhpcy5zdGF0ZS5fc3ZndGV4dGJrZmlsdGVyLmlkKyIpIn10aGlzLl9zdmcudGV4dCh0aGlzLnN0YXRlLl9zdmdncm91cCx0LGUsaSxyKX0sdC5wcm90b3R5cGUubGluZVRvPWZ1bmN0aW9uKHQsZSl7Yy5sb2coIltnZGldIGxpbmVUbzogeD0iK3QrIiB5PSIrZSsiIHdpdGggcGVuICIrdGhpcy5zdGF0ZS5zZWxlY3RlZC5wZW4udG9TdHJpbmcoKSk7dmFyIGk9dGhpcy5fdG9kZXZYKHQpLHM9dGhpcy5fdG9kZXZZKGUpLG49dGhpcy5fdG9kZXZYKHRoaXMuc3RhdGUueCksbz10aGlzLl90b2RldlkodGhpcy5zdGF0ZS55KTt0aGlzLnN0YXRlLng9dCx0aGlzLnN0YXRlLnk9ZSxjLmxvZygiW2dkaV0gbGluZVRvOiBUUkFOU0xBVEVEOiB0b1g9IitpKyIgdG9ZPSIrcysiIGZyb21YPSIrbisiIGZyb21ZPSIrbyksdGhpcy5fcHVzaEdyb3VwKCk7dmFyIHI9dGhpcy5fYXBwbHlPcHRzKG51bGwsITAsITEsITEpO3RoaXMuX3N2Zy5saW5lKHRoaXMuc3RhdGUuX3N2Z2dyb3VwLG4sbyxpLHMscil9LHQucHJvdG90eXBlLm1vdmVUbz1mdW5jdGlvbih0LGUpe2MubG9nKCJbZ2RpXSBtb3ZlVG86IHg9Iit0KyIgeT0iK2UpLHRoaXMuc3RhdGUueD10LHRoaXMuc3RhdGUueT1lfSx0LnByb3RvdHlwZS5wb2x5Z29uPWZ1bmN0aW9uKHQsZSl7Yy5sb2coIltnZGldIHBvbHlnb246IHBvaW50cz0iK3QrIiB3aXRoIHBlbiAiK3RoaXMuc3RhdGUuc2VsZWN0ZWQucGVuLnRvU3RyaW5nKCkrIiBhbmQgYnJ1c2ggIit0aGlzLnN0YXRlLnNlbGVjdGVkLmJydXNoLnRvU3RyaW5nKCkpO2Zvcih2YXIgaT1bXSxzPTA7czx0Lmxlbmd0aDtzKyspe3ZhciBuPXRbc107aS5wdXNoKFt0aGlzLl90b2Rldlgobi54KSx0aGlzLl90b2Rldlkobi55KV0pfWMubG9nKCJbZ2RpXSBwb2x5Z29uOiBUUkFOU0xBVEVEOiBwdHM9IitpKSxlJiZ0aGlzLl9wdXNoR3JvdXAoKTt2YXIgbz17ImZpbGwtcnVsZSI6dGhpcy5zdGF0ZS5wb2x5ZmlsbG1vZGU9PT1jLkdESS5Qb2x5RmlsbE1vZGUuQUxURVJOQVRFPyJldmVub2RkIjoibm9uemVybyJ9O3RoaXMuX2FwcGx5T3B0cyhvLCEwLCEwLCExKSx0aGlzLl9zdmcucG9seWdvbih0aGlzLnN0YXRlLl9zdmdncm91cCxpLG8pfSx0LnByb3RvdHlwZS5wb2x5UG9seWdvbj1mdW5jdGlvbih0KXtjLmxvZygiW2dkaV0gcG9seVBvbHlnb246IHBvbHlnb25zLmxlbmd0aD0iK3QubGVuZ3RoKyIgd2l0aCBwZW4gIit0aGlzLnN0YXRlLnNlbGVjdGVkLnBlbi50b1N0cmluZygpKyIgYW5kIGJydXNoICIrdGhpcy5zdGF0ZS5zZWxlY3RlZC5icnVzaC50b1N0cmluZygpKTtmb3IodmFyIGU9dC5sZW5ndGgsaT0wO2k8ZTtpKyspdGhpcy5wb2x5Z29uKHRbaV0sMD09PWkpfSx0LnByb3RvdHlwZS5wb2x5bGluZT1mdW5jdGlvbih0KXtjLmxvZygiW2dkaV0gcG9seWxpbmU6IHBvaW50cz0iK3QrIiB3aXRoIHBlbiAiK3RoaXMuc3RhdGUuc2VsZWN0ZWQucGVuLnRvU3RyaW5nKCkpO2Zvcih2YXIgZT1bXSxpPTA7aTx0Lmxlbmd0aDtpKyspe3ZhciBzPXRbaV07ZS5wdXNoKFt0aGlzLl90b2Rldlgocy54KSx0aGlzLl90b2Rldlkocy55KV0pfWMubG9nKCJbZ2RpXSBwb2x5bGluZTogVFJBTlNMQVRFRDogcHRzPSIrZSksdGhpcy5fcHVzaEdyb3VwKCk7dmFyIG49dGhpcy5fYXBwbHlPcHRzKHtmaWxsOiJub25lIn0sITAsITEsITEpO3RoaXMuX3N2Zy5wb2x5bGluZSh0aGlzLnN0YXRlLl9zdmdncm91cCxlLG4pfSx0LnByb3RvdHlwZS5lbGxpcHNlPWZ1bmN0aW9uKHQpe2MubG9nKCJbZ2RpXSBlbGxpcHNlOiByZWN0PSIrdC50b1N0cmluZygpKyIgd2l0aCBwZW4gIit0aGlzLnN0YXRlLnNlbGVjdGVkLnBlbi50b1N0cmluZygpKyIgYW5kIGJydXNoICIrdGhpcy5zdGF0ZS5zZWxlY3RlZC5icnVzaC50b1N0cmluZygpKTt2YXIgZT10aGlzLl90b2RldlkodC5ib3R0b20pLGk9dGhpcy5fdG9kZXZYKHQucmlnaHQpLHM9dGhpcy5fdG9kZXZZKHQudG9wKSxuPXRoaXMuX3RvZGV2WCh0LmxlZnQpO2MubG9nKCJbZ2RpXSBlbGxpcHNlOiBUUkFOU0xBVEVEOiBib3R0b209IitlKyIgcmlnaHQ9IitpKyIgdG9wPSIrcysiIGxlZnQ9IituKSx0aGlzLl9wdXNoR3JvdXAoKTt2YXIgbz0oaS1uKS8yLHI9KGUtcykvMixhPXRoaXMuX2FwcGx5T3B0cyhudWxsLCEwLCEwLCExKTt0aGlzLl9zdmcuZWxsaXBzZSh0aGlzLnN0YXRlLl9zdmdncm91cCxuK28scytyLG8scixhKX0sdC5wcm90b3R5cGUuZXhjbHVkZUNsaXBSZWN0PWZ1bmN0aW9uKHQpe2MubG9nKCJbZ2RpXSBleGNsdWRlQ2xpcFJlY3Q6IHJlY3Q9Iit0LnRvU3RyaW5nKCkpLHRoaXMuX2dldENsaXBSZ24oKS5zdWJ0cmFjdCh0KX0sdC5wcm90b3R5cGUuaW50ZXJzZWN0Q2xpcFJlY3Q9ZnVuY3Rpb24odCl7Yy5sb2coIltnZGldIGludGVyc2VjdENsaXBSZWN0OiByZWN0PSIrdC50b1N0cmluZygpKSx0aGlzLl9nZXRDbGlwUmduKCkuaW50ZXJzZWN0KHQpfSx0LnByb3RvdHlwZS5vZmZzZXRDbGlwUmduPWZ1bmN0aW9uKHQsZSl7Yy5sb2coIltnZGldIG9mZnNldENsaXBSZ246IG9mZlg9Iit0KyIgb2ZmWT0iK2UpLHRoaXMuX2dldENsaXBSZ24oKS5vZmZzZXQodCxlKX0sdC5wcm90b3R5cGUuc2V0VGV4dEFsaWduPWZ1bmN0aW9uKHQpe2MubG9nKCJbZ2RpXSBzZXRUZXh0QWxpZ246IHRleHRBbGlnbm1lbnRNb2RlPTB4Iit0LnRvU3RyaW5nKDE2KSksdGhpcy5zdGF0ZS50ZXh0YWxpZ249dH0sdC5wcm90b3R5cGUuc2V0QmtNb2RlPWZ1bmN0aW9uKHQpe2MubG9nKCJbZ2RpXSBzZXRCa01vZGU6IGJrTW9kZT0weCIrdC50b1N0cmluZygxNikpLHRoaXMuc3RhdGUuYmttb2RlPXR9LHQucHJvdG90eXBlLnNldFRleHRDb2xvcj1mdW5jdGlvbih0KXtjLmxvZygiW2dkaV0gc2V0VGV4dENvbG9yOiB0ZXh0Q29sb3I9Iit0LnRvU3RyaW5nKCkpLHRoaXMuc3RhdGUudGV4dGNvbG9yPXR9LHQucHJvdG90eXBlLnNldEJrQ29sb3I9ZnVuY3Rpb24odCl7Yy5sb2coIltnZGldIHNldEJrQ29sb3I6IGJrQ29sb3I9Iit0LnRvU3RyaW5nKCkpLHRoaXMuc3RhdGUuYmtjb2xvcj10LHRoaXMuc3RhdGUuX3N2Z3RleHRia2ZpbHRlcj1udWxsfSx0LnByb3RvdHlwZS5zZXRQb2x5RmlsbE1vZGU9ZnVuY3Rpb24odCl7Yy5sb2coIltnZGldIHNldFBvbHlGaWxsTW9kZTogcG9seUZpbGxNb2RlPSIrdCksdGhpcy5zdGF0ZS5wb2x5ZmlsbG1vZGU9dH0sdC5wcm90b3R5cGUuY3JlYXRlQnJ1c2g9ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fc3RvcmVPYmplY3QodCk7Yy5sb2coIltnZGldIGNyZWF0ZUJydXNoOiBicnVzaD0iK3QudG9TdHJpbmcoKSsiIHdpdGggaGFuZGxlICIrZSl9LHQucHJvdG90eXBlLmNyZWF0ZUZvbnQ9ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fc3RvcmVPYmplY3QodCk7Yy5sb2coIltnZGldIGNyZWF0ZUZvbnQ6IGZvbnQ9Iit0LnRvU3RyaW5nKCkrIiB3aXRoIGhhbmRsZSAiK2UpfSx0LnByb3RvdHlwZS5jcmVhdGVQZW49ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fc3RvcmVPYmplY3QodCk7Yy5sb2coIltnZGldIGNyZWF0ZVBlbjogcGVuPSIrdC50b1N0cmluZygpKyIgd2lkdGggaGFuZGxlICIrZSl9LHQucHJvdG90eXBlLmNyZWF0ZVBhbGV0dGU9ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fc3RvcmVPYmplY3QodCk7Yy5sb2coIltnZGldIGNyZWF0ZVBhbGV0dGU6IHBhbGV0dGU9Iit0LnRvU3RyaW5nKCkrIiB3aWR0aCBoYW5kbGUgIitlKX0sdC5wcm90b3R5cGUuY3JlYXRlUmVnaW9uPWZ1bmN0aW9uKHQpe3ZhciBlPXRoaXMuX3N0b3JlT2JqZWN0KHQpO2MubG9nKCJbZ2RpXSBjcmVhdGVSZWdpb246IHJlZ2lvbj0iK3QudG9TdHJpbmcoKSsiIHdpZHRoIGhhbmRsZSAiK2UpfSx0LnByb3RvdHlwZS5jcmVhdGVQYXR0ZXJuQnJ1c2g9ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fc3RvcmVPYmplY3QodCk7Yy5sb2coIltnZGldIGNyZWF0ZVJlZ2lvbjogcmVnaW9uPSIrdC50b1N0cmluZygpKyIgd2lkdGggaGFuZGxlICIrZSl9LHQucHJvdG90eXBlLnNlbGVjdE9iamVjdD1mdW5jdGlvbih0LGUpe3ZhciBpPXRoaXMuX2dldE9iamVjdCh0KTtudWxsPT1pfHxudWxsIT1lJiZpLnR5cGUhPT1lP2MubG9nKCJbZ2RpXSBzZWxlY3RPYmplY3Q6IG9iaklkeD0iK3QrKGk/IiBpbnZhbGlkIG9iamVjdCB0eXBlOiAiK2kudHlwZToiW2ludmFsaWQgaW5kZXhdIikpOih0aGlzLl9zZWxlY3RPYmplY3QoaSksYy5sb2coIltnZGldIHNlbGVjdE9iamVjdDogb2JqSWR4PSIrdCsoaT8iIHNlbGVjdGVkICIraS50eXBlKyI6ICIraS50b1N0cmluZygpOiJbaW52YWxpZCBpbmRleF0iKSkpfSx0LnByb3RvdHlwZS5kZWxldGVPYmplY3Q9ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5fZGVsZXRlT2JqZWN0KHQpO2MubG9nKCJbZ2RpXSBkZWxldGVPYmplY3Q6IG9iaklkeD0iK3QrKGU/IiBkZWxldGVkIG9iamVjdCI6IltpbnZhbGlkIGluZGV4XSIpKX0sdC5wcm90b3R5cGUuX3B1c2hHcm91cD1mdW5jdGlvbigpe2lmKG51bGw9PXRoaXMuc3RhdGUuX3N2Z2dyb3VwfHx0aGlzLnN0YXRlLl9zdmdjbGlwQ2hhbmdlZCl7dGhpcy5zdGF0ZS5fc3ZnY2xpcENoYW5nZWQ9ITEsdGhpcy5zdGF0ZS5fc3ZndGV4dGJrZmlsdGVyPW51bGw7dmFyIHQ9e3ZpZXdCb3g6W3RoaXMuc3RhdGUudngsdGhpcy5zdGF0ZS52eSx0aGlzLnN0YXRlLnZ3LHRoaXMuc3RhdGUudmhdLmpvaW4oIiAiKSxwcmVzZXJ2ZUFzcGVjdFJhdGlvOiJub25lIn07bnVsbCE9dGhpcy5zdGF0ZS5jbGlwPyhjLmxvZygiW2dkaV0gbmV3IHN2ZyB4PSIrdGhpcy5zdGF0ZS52eCsiIHk9Iit0aGlzLnN0YXRlLnZ5KyIgd2lkdGg9Iit0aGlzLnN0YXRlLnZ3KyIgaGVpZ2h0PSIrdGhpcy5zdGF0ZS52aCsiIHdpdGggY2xpcHBpbmciKSx0WyJjbGlwLXBhdGgiXT0idXJsKCMiK3RoaXMuX2dldFN2Z0NsaXBQYXRoRm9yUmVnaW9uKHRoaXMuc3RhdGUuY2xpcCkrIikiKTpjLmxvZygiW2dkaV0gbmV3IHN2ZyB4PSIrdGhpcy5zdGF0ZS52eCsiIHk9Iit0aGlzLnN0YXRlLnZ5KyIgd2lkdGg9Iit0aGlzLnN0YXRlLnZ3KyIgaGVpZ2h0PSIrdGhpcy5zdGF0ZS52aCsiIHdpdGhvdXQgY2xpcHBpbmciKSx0aGlzLnN0YXRlLl9zdmdncm91cD10aGlzLl9zdmcuc3ZnKHRoaXMuc3RhdGUuX3N2Z2dyb3VwLHRoaXMuc3RhdGUudngsdGhpcy5zdGF0ZS52eSx0aGlzLnN0YXRlLnZ3LHRoaXMuc3RhdGUudmgsdCl9fSx0LnByb3RvdHlwZS5fc3RvcmVPYmplY3Q9ZnVuY3Rpb24odCl7Zm9yKHZhciBlPTA7bnVsbCE9dGhpcy5vYmplY3RzW2UudG9TdHJpbmcoKV0mJmU8PTY1NTM1OyllKys7cmV0dXJuIGU+NjU1MzU/KGMubG9nKCJbZ2RpXSBUb28gbWFueSBvYmplY3RzISIpLC0xKToodGhpcy5vYmplY3RzW2UudG9TdHJpbmcoKV09dCxlKX0sdC5wcm90b3R5cGUuX2dldE9iamVjdD1mdW5jdGlvbih0KXt2YXIgZT10aGlzLm9iamVjdHNbdC50b1N0cmluZygpXTtyZXR1cm4gbnVsbD09ZSYmYy5sb2coIltnZGldIE5vIG9iamVjdCB3aXRoIGhhbmRsZSAiK3QpLGV9LHQucHJvdG90eXBlLl9nZXRTdmdEZWY9ZnVuY3Rpb24oKXtyZXR1cm4gbnVsbD09dGhpcy5fc3ZnZGVmcyYmKHRoaXMuX3N2Z2RlZnM9dGhpcy5fc3ZnLmRlZnMoKSksdGhpcy5fc3ZnZGVmc30sdC5wcm90b3R5cGUuX2dldFN2Z0NsaXBQYXRoRm9yUmVnaW9uPWZ1bmN0aW9uKHQpe2Zvcih2YXIgZSBpbiB0aGlzLl9zdmdDbGlwUGF0aHMpaWYodGhpcy5fc3ZnQ2xpcFBhdGhzW2VdPT09dClyZXR1cm4gZTt2YXIgaT1jLl9tYWtlVW5pcXVlSWQoImMiKSxzPXRoaXMuX3N2Zy5jbGlwUGF0aCh0aGlzLl9nZXRTdmdEZWYoKSxpLCJ1c2VyU3BhY2VPblVzZSIpO3N3aXRjaCh0LmNvbXBsZXhpdHkpe2Nhc2UgMTp0aGlzLl9zdmcucmVjdChzLHRoaXMuX3RvZGV2WCh0LmJvdW5kcy5sZWZ0KSx0aGlzLl90b2RldlkodC5ib3VuZHMudG9wKSx0aGlzLl90b2RldlcodC5ib3VuZHMucmlnaHQtdC5ib3VuZHMubGVmdCksdGhpcy5fdG9kZXZIKHQuYm91bmRzLmJvdHRvbS10LmJvdW5kcy50b3ApLHtmaWxsOiJibGFjayIsInN0cm9rZS13aWR0aCI6MH0pO2JyZWFrO2Nhc2UgMjpmb3IodmFyIG49MDtuPHQuc2NhbnMubGVuZ3RoO24rKylmb3IodmFyIG89dC5zY2Fuc1tuXSxyPTA7cjxvLnNjYW5saW5lcy5sZW5ndGg7cisrKXt2YXIgYT1vLnNjYW5saW5lc1tyXTt0aGlzLl9zdmcucmVjdChzLHRoaXMuX3RvZGV2WChhLmxlZnQpLHRoaXMuX3RvZGV2WShvLnRvcCksdGhpcy5fdG9kZXZXKGEucmlnaHQtYS5sZWZ0KSx0aGlzLl90b2Rldkgoby5ib3R0b20tby50b3ApLHtmaWxsOiJibGFjayIsInN0cm9rZS13aWR0aCI6MH0pfX1yZXR1cm4gdGhpcy5fc3ZnQ2xpcFBhdGhzW2ldPXQsaX0sdC5wcm90b3R5cGUuX2dldFN2Z1BhdHRlcm5Gb3JCcnVzaD1mdW5jdGlvbih0KXtmb3IodmFyIGUgaW4gdGhpcy5fc3ZnUGF0dGVybnMpaWYodGhpcy5fc3ZnUGF0dGVybnNbZV09PT10KXJldHVybiBlO3ZhciBpLHMsbjtzd2l0Y2godC5zdHlsZSl7Y2FzZSBjLkdESS5CcnVzaFN0eWxlLkJTX1BBVFRFUk46aT10LnBhdHRlcm4uZ2V0V2lkdGgoKSxzPXQucGF0dGVybi5nZXRIZWlnaHQoKTticmVhaztjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfRElCUEFUVEVSTlBUOmk9dC5kaWJwYXR0ZXJucHQuZ2V0V2lkdGgoKSxzPXQuZGlicGF0dGVybnB0LmdldEhlaWdodCgpLG49dC5kaWJwYXR0ZXJucHQuYmFzZTY0cmVmKCk7YnJlYWs7ZGVmYXVsdDp0aHJvdyBuZXcgYSgiSW52YWxpZCBicnVzaCBzdHlsZSIpfXZhciBvPWMuX21ha2VVbmlxdWVJZCgicCIpLHI9dGhpcy5fc3ZnLnBhdHRlcm4odGhpcy5fZ2V0U3ZnRGVmKCksbywwLDAsaSxzLHtwYXR0ZXJuVW5pdHM6InVzZXJTcGFjZU9uVXNlIn0pO3JldHVybiB0aGlzLl9zdmcuaW1hZ2UociwwLDAsaSxzLG4pLHRoaXMuX3N2Z1BhdHRlcm5zW29dPXQsb30sdC5wcm90b3R5cGUuX3NlbGVjdE9iamVjdD1mdW5jdGlvbih0KXt0aGlzLnN0YXRlLnNlbGVjdGVkW3QudHlwZV09dCwicmVnaW9uIj09PXQudHlwZSYmKHRoaXMuc3RhdGUuX3N2Z2NsaXBDaGFuZ2VkPSEwKX0sdC5wcm90b3R5cGUuX2RlbGV0ZU9iamVjdD1mdW5jdGlvbih0KXt2YXIgZT10aGlzLm9iamVjdHNbdC50b1N0cmluZygpXTtpZihudWxsIT1lKXtmb3IodmFyIGk9MDtpPHRoaXMuc3RhdGVzdGFjay5sZW5ndGg7aSsrKXt2YXIgcz10aGlzLnN0YXRlc3RhY2tbaV07cy5zZWxlY3RlZFtlLnR5cGVdPT09ZSYmKHMuc2VsZWN0ZWRbZS50eXBlXT10aGlzLmRlZk9iamVjdHNbZS50eXBlXS5jbG9uZSgpKX1yZXR1cm4gZGVsZXRlIHRoaXMub2JqZWN0c1t0LnRvU3RyaW5nKCldLCEwfXJldHVybiBjLmxvZygiW2dkaV0gQ2Fubm90IGRlbGV0ZSBvYmplY3Qgd2l0aCBpbnZhbGlkIGhhbmRsZSAiK3QpLCExfSx0LnByb3RvdHlwZS5fZ2V0Q2xpcFJnbj1mdW5jdGlvbigpe3ZhciB0LGUsaSxzLG47cmV0dXJuIG51bGwhPXRoaXMuc3RhdGUuY2xpcD90aGlzLnN0YXRlLm93bmNsaXB8fCh0aGlzLnN0YXRlLmNsaXA9dGhpcy5zdGF0ZS5jbGlwLmNsb25lKCkpOm51bGwhPXRoaXMuc3RhdGUuc2VsZWN0ZWQucmVnaW9uP3RoaXMuc3RhdGUuY2xpcD10aGlzLnN0YXRlLnNlbGVjdGVkLnJlZ2lvbi5jbG9uZSgpOnRoaXMuc3RhdGUuY2xpcD0odD10aGlzLnN0YXRlLnd4LGU9dGhpcy5zdGF0ZS53eSxpPXRoaXMuc3RhdGUud3grdGhpcy5zdGF0ZS53dyxzPXRoaXMuc3RhdGUud3krdGhpcy5zdGF0ZS53aCwobj1uZXcgRShudWxsLG51bGwpKS5ib3VuZHM9bmV3IGQobnVsbCx0LGUsaSxzKSxuLl91cGRhdGVDb21wbGV4aXR5KCksbiksdGhpcy5zdGF0ZS5vd25jbGlwPSEwLHRoaXMuc3RhdGUuY2xpcH0sdC5wcm90b3R5cGUuX3RvZGV2WD1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5mbG9vcigodC10aGlzLnN0YXRlLnd4KSoodGhpcy5zdGF0ZS52dy90aGlzLnN0YXRlLnd3KSkrdGhpcy5zdGF0ZS52eH0sdC5wcm90b3R5cGUuX3RvZGV2WT1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5mbG9vcigodC10aGlzLnN0YXRlLnd5KSoodGhpcy5zdGF0ZS52aC90aGlzLnN0YXRlLndoKSkrdGhpcy5zdGF0ZS52eX0sdC5wcm90b3R5cGUuX3RvZGV2Vz1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5mbG9vcih0Kih0aGlzLnN0YXRlLnZ3L3RoaXMuc3RhdGUud3cpKSt0aGlzLnN0YXRlLnZ4fSx0LnByb3RvdHlwZS5fdG9kZXZIPWZ1bmN0aW9uKHQpe3JldHVybiBNYXRoLmZsb29yKHQqKHRoaXMuc3RhdGUudmgvdGhpcy5zdGF0ZS53aCkpK3RoaXMuc3RhdGUudnl9LHQucHJvdG90eXBlLl90b2xvZ2ljYWxYPWZ1bmN0aW9uKHQpe3JldHVybiBNYXRoLmZsb29yKCh0LXRoaXMuc3RhdGUudngpLyh0aGlzLnN0YXRlLnZ3L3RoaXMuc3RhdGUud3cpKSt0aGlzLnN0YXRlLnd4fSx0LnByb3RvdHlwZS5fdG9sb2dpY2FsWT1mdW5jdGlvbih0KXtyZXR1cm4gTWF0aC5mbG9vcigodC10aGlzLnN0YXRlLnZ5KS8odGhpcy5zdGF0ZS52aC90aGlzLnN0YXRlLndoKSkrdGhpcy5zdGF0ZS53eX0sdC5wcm90b3R5cGUuX3RvbG9naWNhbFc9ZnVuY3Rpb24odCl7cmV0dXJuIE1hdGguZmxvb3IodC8odGhpcy5zdGF0ZS52dy90aGlzLnN0YXRlLnd3KSkrdGhpcy5zdGF0ZS53eH0sdC5wcm90b3R5cGUuX3RvbG9naWNhbEg9ZnVuY3Rpb24odCl7cmV0dXJuIE1hdGguZmxvb3IodC8odGhpcy5zdGF0ZS52aC90aGlzLnN0YXRlLndoKSkrdGhpcy5zdGF0ZS53eX0sdC5wcm90b3R5cGUuX2FwcGx5T3B0cz1mdW5jdGlvbih0LGUsaSxzKXtpZihudWxsPT10JiYodD17fSksZSl7dmFyIG49dGhpcy5zdGF0ZS5zZWxlY3RlZC5wZW47aWYobi5zdHlsZSE9PWMuR0RJLlBlblN0eWxlLlBTX05VTEwpe3Quc3Ryb2tlPSIjIituLmNvbG9yLnRvSGV4KCksdFsic3Ryb2tlLXdpZHRoIl09dGhpcy5fdG9kZXZXKG4ud2lkdGgueCk7dmFyIG89dm9pZCAwOzAhPShuLmxpbmVjYXAmYy5HREkuUGVuU3R5bGUuUFNfRU5EQ0FQX1NRVUFSRSk/KHRbInN0cm9rZS1saW5lY2FwIl09InNxdWFyZSIsbz0xKTowIT0obi5saW5lY2FwJmMuR0RJLlBlblN0eWxlLlBTX0VORENBUF9GTEFUKT8odFsic3Ryb2tlLWxpbmVjYXAiXT0iYnV0dCIsbz10WyJzdHJva2Utd2lkdGgiXSk6KHRbInN0cm9rZS1saW5lY2FwIl09InJvdW5kIixvPTEpLDAhPShuLmpvaW4mYy5HREkuUGVuU3R5bGUuUFNfSk9JTl9CRVZFTCk/dFsic3Ryb2tlLWxpbmVqb2luIl09ImJldmVsIjowIT0obi5qb2luJmMuR0RJLlBlblN0eWxlLlBTX0pPSU5fTUlURVIpP3RbInN0cm9rZS1saW5lam9pbiJdPSJtaXRlciI6dFsic3Ryb2tlLWxpbmVqb2luIl09InJvdW5kIjt2YXIgcj00KnRbInN0cm9rZS13aWR0aCJdLGE9Mip0WyJzdHJva2Utd2lkdGgiXTtzd2l0Y2gobi5zdHlsZSl7Y2FzZSBjLkdESS5QZW5TdHlsZS5QU19EQVNIOnRbInN0cm9rZS1kYXNoYXJyYXkiXT1bcixhXS50b1N0cmluZygpO2JyZWFrO2Nhc2UgYy5HREkuUGVuU3R5bGUuUFNfRE9UOnRbInN0cm9rZS1kYXNoYXJyYXkiXT1bbyxhXS50b1N0cmluZygpO2JyZWFrO2Nhc2UgYy5HREkuUGVuU3R5bGUuUFNfREFTSERPVDp0WyJzdHJva2UtZGFzaGFycmF5Il09W3IsYSxvLGFdLnRvU3RyaW5nKCk7YnJlYWs7Y2FzZSBjLkdESS5QZW5TdHlsZS5QU19EQVNIRE9URE9UOnRbInN0cm9rZS1kYXNoYXJyYXkiXT1bcixhLG8sYSxvLGFdLnRvU3RyaW5nKCl9fX1pZihpKXt2YXIgaD10aGlzLnN0YXRlLnNlbGVjdGVkLmJydXNoO3N3aXRjaChoLnN0eWxlKXtjYXNlIGMuR0RJLkJydXNoU3R5bGUuQlNfU09MSUQ6dC5maWxsPSIjIitoLmNvbG9yLnRvSGV4KCk7YnJlYWs7Y2FzZSBjLkdESS5CcnVzaFN0eWxlLkJTX1BBVFRFUk46Y2FzZSBjLkdESS5CcnVzaFN0eWxlLkJTX0RJQlBBVFRFUk5QVDp0LmZpbGw9InVybCgjIit0aGlzLl9nZXRTdmdQYXR0ZXJuRm9yQnJ1c2goaCkrIikiO2JyZWFrO2Nhc2UgYy5HREkuQnJ1c2hTdHlsZS5CU19OVUxMOnQuZmlsbD0ibm9uZSI7YnJlYWs7ZGVmYXVsdDpjLmxvZygiW2dkaV0gdW5zdXBwb3J0ZWQgYnJ1c2ggc3R5bGU6ICIraC5zdHlsZSksdC5maWxsPSJub25lIn19aWYocyl7dmFyIGw9dGhpcy5zdGF0ZS5zZWxlY3RlZC5mb250O3RbImZvbnQtZmFtaWx5Il09bC5mYWNlbmFtZSx0WyJmb250LXNpemUiXT10aGlzLl90b2RldkgoTWF0aC5hYnMobC5oZWlnaHQpKSx0LmZpbGw9IiMiK3RoaXMuc3RhdGUudGV4dGNvbG9yLnRvSGV4KCl9cmV0dXJuIHR9LHR9KCksRz1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCxlKXt0aGlzLl9yZWNvcmRzPVtdO3ZhciBpPSExLHM9ZSxuPWZ1bmN0aW9uKCl7dC5zZWVrKHMpO3ZhciBlPXQucmVhZFVpbnQzMigpO2lmKGU8Myl0aHJvdyBuZXcgYSgiSW52YWxpZCByZWNvcmQgc2l6ZSIpO3ZhciBuPXQucmVhZFVpbnQxNigpO3N3aXRjaChuKXtjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9FT0Y6cmV0dXJuIGk9ITAsImJyZWFrLW1haW5fbG9vcCI7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUTUFQTU9ERTp2YXIgcj10LnJlYWRVaW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0TWFwTW9kZShyKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRXSU5ET1dPUkc6dmFyIGg9dC5yZWFkSW50MTYoKSxsPXQucmVhZEludDE2KCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNldFdpbmRvd09yZyhsLGgpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1NFVFdJTkRPV0VYVDp2YXIgVD10LnJlYWRJbnQxNigpLGY9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0V2luZG93RXh0KGYsVCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfT0ZGU0VUV0lORE9XT1JHOnZhciBnPXQucmVhZEludDE2KCksXz10LnJlYWRJbnQxNigpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5vZmZzZXRXaW5kb3dPcmcoXyxnKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRWSUVXUE9SVE9SRzp2YXIgeT10LnJlYWRJbnQxNigpLFM9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0Vmlld3BvcnRPcmcoUyx5KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRWSUVXUE9SVEVYVDp2YXIgST10LnJlYWRJbnQxNigpLEE9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0Vmlld3BvcnRFeHQoQSxJKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9PRkZTRVRWSUVXUE9SVE9SRzp2YXIgdz10LnJlYWRJbnQxNigpLFA9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Qub2Zmc2V0Vmlld3BvcnRPcmcoUCx3KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TQVZFREM6by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNhdmVEQygpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1JFU1RPUkVEQzp2YXIgQz10LnJlYWRJbnQxNigpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5yZXN0b3JlREMoQyl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUU1RSRVRDSEJMVE1PREU6dmFyIE49dC5yZWFkVWludDE2KCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNldFN0cmV0Y2hCbHRNb2RlKE4pfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0RJQkJJVEJMVDp2YXIgRz0zKyhuPj44KSE9PWUseD10LnJlYWRVaW50MTYoKXx0LnJlYWRVaW50MTYoKTw8MTYsQj10LnJlYWRJbnQxNigpLEw9dC5yZWFkSW50MTYoKTtHfHx0LnNraXAoMik7dmFyIGs9dC5yZWFkSW50MTYoKSxVPXQucmVhZEludDE2KCksSD10LnJlYWRJbnQxNigpLEY9dC5yZWFkSW50MTYoKTtpZihHKXt2YXIgaj0yKmUtKHQucG9zLXMpLFg9bmV3IHYodCxqKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QuZGliQml0cyhMLEIsRixILFUsayx4LFgpfSkpfWJyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0RJQlNUUkVUQ0hCTFQ6Rz0zKyhuPj44KSE9PWU7dmFyIFc9dC5yZWFkVWludDE2KCl8dC5yZWFkVWludDE2KCk8PDE2LFk9dC5yZWFkSW50MTYoKSxWPXQucmVhZEludDE2KCksej10LnJlYWRJbnQxNigpLEo9dC5yZWFkSW50MTYoKSxaPXQucmVhZEludDE2KCkscT10LnJlYWRJbnQxNigpLEs9dC5yZWFkSW50MTYoKSxRPXQucmVhZEludDE2KCksJD0oaj0yKmUtKHQucG9zLXMpLG5ldyB2KHQsaikpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5zdHJldGNoRGliQml0cyhKLHosVixZLFEsSyxxLFosVywkKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TVFJFVENIRElCOnZhciB0dD10LnJlYWRVaW50MTYoKXx0LnJlYWRVaW50MTYoKTw8MTYsZXQ9dC5yZWFkSW50MTYoKSxpdD10LnJlYWRJbnQxNigpLHN0PXQucmVhZEludDE2KCksbnQ9dC5yZWFkSW50MTYoKSxvdD10LnJlYWRJbnQxNigpLHJ0PXQucmVhZEludDE2KCksYXQ9dC5yZWFkSW50MTYoKSxodD10LnJlYWRJbnQxNigpLGx0PXQucmVhZEludDE2KCksY3Q9KGo9MiplLSh0LnBvcy1zKSxuZXcgdih0LGopKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc3RyZXRjaERpYihvdCxudCxzdCxpdCxsdCxodCxhdCxydCx0dCxldCxjdCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfRVNDQVBFOnZhciBwdD10LnJlYWRVaW50MTYoKSx1dD10LnJlYWRVaW50MTYoKSxkdD10LnBvcyxUdD1uZXcgcCh0LGR0KTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QuZXNjYXBlKHB0LFR0LGR0LHV0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRURVhUQUxJR046dmFyIGZ0PXQucmVhZFVpbnQxNigpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5zZXRUZXh0QWxpZ24oZnQpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1NFVEJLTU9ERTp2YXIgRXQ9dC5yZWFkVWludDE2KCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNldEJrTW9kZShFdCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUVEVYVENPTE9SOnZhciBndD1uZXcgUih0KTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0VGV4dENvbG9yKGd0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRCS0NPTE9SOnZhciBfdD1uZXcgUih0KTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0QmtDb2xvcihfdCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQ1JFQVRFQlJVU0hJTkRJUkVDVDpqPTIqZS0odC5wb3Mtcyk7dmFyIHl0PW5ldyBEKHQsaiwhMSk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmNyZWF0ZUJydXNoKHl0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9ESUJDUkVBVEVQQVRURVJOQlJVU0g6aj0yKmUtKHQucG9zLXMpO3ZhciBTdD1uZXcgRCh0LGosITApO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5jcmVhdGVCcnVzaChTdCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQ1JFQVRFUEVOSU5ESVJFQ1Q6dmFyIEl0PW5ldyBNKHQpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5jcmVhdGVQZW4oSXQpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0NSRUFURUZPTlRJTkRJUkVDVDpqPTIqZS0odC5wb3Mtcyk7dmFyIHZ0PW5ldyBPKHQsaik7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmNyZWF0ZUZvbnQodnQpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1NFTEVDVE9CSkVDVDp2YXIgQXQ9dC5yZWFkVWludDE2KCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNlbGVjdE9iamVjdChBdCxudWxsKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRUxFQ1RQQUxFVFRFOnZhciBidD10LnJlYWRVaW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2VsZWN0T2JqZWN0KGJ0LCJwYWxldHRlIil9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VMRUNUQ0xJUFJFR0lPTjp2YXIgd3Q9dC5yZWFkVWludDE2KCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnNlbGVjdE9iamVjdCh3dCwicmVnaW9uIil9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfREVMRVRFT0JKRUNUOnZhciBSdD10LnJlYWRVaW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QuZGVsZXRlT2JqZWN0KFJ0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9SRUNUQU5HTEU6dmFyIE90PW5ldyBkKHQpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5yZWN0YW5nbGUoT3QsMCwwKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9ST1VORFJFQ1Q6dmFyIER0PXQucmVhZEludDE2KCksTXQ9dC5yZWFkSW50MTYoKSxQdD1uZXcgZCh0KTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QucmVjdGFuZ2xlKFB0LE10LER0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9MSU5FVE86dmFyIG10PXQucmVhZEludDE2KCksQ3Q9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QubGluZVRvKEN0LG10KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9NT1ZFVE86dmFyIE50PXQucmVhZEludDE2KCksR3Q9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QubW92ZVRvKEd0LE50KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9URVhUT1VUOmlmKChIdD10LnJlYWRJbnQxNigpKT4wKXt2YXIgeHQ9dC5yZWFkU3RyaW5nKEh0KTt0LnNraXAoSHQlMik7dmFyIEJ0PXQucmVhZEludDE2KCksTHQ9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QudGV4dE91dChMdCxCdCx4dCl9KSl9YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfRVhUVEVYVE9VVDp2YXIga3Q9dC5yZWFkSW50MTYoKSxVdD10LnJlYWRJbnQxNigpLEh0PXQucmVhZEludDE2KCksRnQ9dC5yZWFkVWludDE2KCksanQ9bnVsbCxYdD1udWxsOzIqZT09PTE0K0h0K0h0JTImJihqdD0hMSxYdD0hMSksMiplPT09MjIrSHQrSHQlMiYmKGp0PSEwLFh0PSExKSwyKmU9PT0xNCtIdCtIdCUyKzIqSHQmJihqdD0hMSxYdD0hMCksMiplPT09MjIrSHQrSHQlMisyKkh0JiYoanQ9ITAsWHQ9ITApO3ZhciBXdD1qdD9uZXcgZCh0KTpudWxsO2lmKEh0PjApe3ZhciBZdD10LnJlYWRTdHJpbmcoSHQpO3Quc2tpcChIdCUyKTt2YXIgVnQ9W107aWYoWHQpZm9yKHZhciB6dD0wO3p0PFl0Lmxlbmd0aDt6dCsrKVZ0LnB1c2godC5yZWFkSW50MTYoKSk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmV4dFRleHRPdXQoVXQsa3QsWXQsRnQsV3QsVnQpfSkpfWJyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0VYQ0xVREVDTElQUkVDVDp2YXIgSnQ9bmV3IGQodCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmV4Y2x1ZGVDbGlwUmVjdChKdCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfSU5URVJTRUNUQ0xJUFJFQ1Q6dmFyIFp0PW5ldyBkKHQpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5pbnRlcnNlY3RDbGlwUmVjdChadCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfUE9MWUdPTjpmb3IodmFyIHF0PXQucmVhZEludDE2KCksS3Q9W107cXQ+MDspS3QucHVzaChuZXcgdSh0KSkscXQtLTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QucG9seWdvbihLdCwhMCl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUUE9MWUZJTExNT0RFOnZhciBRdD10LnJlYWRVaW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Quc2V0UG9seUZpbGxNb2RlKFF0KX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9QT0xZUE9MWUdPTjpxdD10LnJlYWRVaW50MTYoKTt2YXIgJHQ9W107Zm9yKHp0PTA7enQ8cXQ7enQrKykkdC5wdXNoKHQucmVhZFVpbnQxNigpKTt2YXIgdGU9W107Zm9yKHp0PTA7enQ8cXQ7enQrKyl7Zm9yKHZhciBlZT0kdFt6dF0saWU9W10sc2U9MDtzZTxlZTtzZSsrKWllLnB1c2gobmV3IHUodCkpO3RlLnB1c2goaWUpfW8uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5wb2x5UG9seWdvbih0ZSl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfUE9MWUxJTkU6cXQ9dC5yZWFkSW50MTYoKTtmb3IodmFyIG5lPVtdO3F0PjA7KW5lLnB1c2gobmV3IHUodCkpLHF0LS07by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LnBvbHlsaW5lKG5lKX0pKTticmVhaztjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9FTExJUFNFOnZhciBvZT1uZXcgZCh0KTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3QuZWxsaXBzZShvZSl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQ1JFQVRFUEFMRVRURTp2YXIgcmU9bmV3IG0odCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmNyZWF0ZVBhbGV0dGUocmUpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0NSRUFURVJFR0lPTjp2YXIgYWU9bmV3IEUodCk7by5fcmVjb3Jkcy5wdXNoKChmdW5jdGlvbih0KXt0LmNyZWF0ZVJlZ2lvbihhZSl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQ1JFQVRFUEFUVEVSTkJSVVNIOmo9MiplLSh0LnBvcy1zKTt2YXIgaGU9bmV3IGIodCxqKSxsZT1uZXcgRCh0LGosaGUpO28uX3JlY29yZHMucHVzaCgoZnVuY3Rpb24odCl7dC5jcmVhdGVQYXR0ZXJuQnJ1c2gobGUpfSkpO2JyZWFrO2Nhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX09GRlNFVENMSVBSR046dmFyIGNlPXQucmVhZEludDE2KCkscGU9dC5yZWFkSW50MTYoKTtvLl9yZWNvcmRzLnB1c2goKGZ1bmN0aW9uKHQpe3Qub2Zmc2V0Q2xpcFJnbihwZSxjZSl9KSk7YnJlYWs7Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfUkVBTElaRVBBTEVUVEU6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUUEFMRU5UUklFUzpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRST1AyOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1NFVFJFTEFCUzpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRURVhUQ0hBUkVYVFJBOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1JFU0laRVBBTEVUVEU6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUTEFZT1VUOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0ZJTExSRUdJT046Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUTUFQUEVSRkxBR1M6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0VUVEVYVEpVU1RJRklDQVRJT046Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0NBTEVXSU5ET1dFWFQ6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU0NBTEVWSUVXUE9SVEVYVDpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9GTE9PREZJTEw6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfRlJBTUVSRUdJT046Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQU5JTUFURVBBTEVUVEU6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfRVhURkxPT0RGSUxMOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1NFVFBJWEVMOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX1BBVEJMVDpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9QSUU6Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfU1RSRVRDSEJMVDpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9JTlZFUlRSRUdJT046Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfUEFJTlRSRUdJT046Y2FzZSBjLkdESS5SZWNvcmRUeXBlLk1FVEFfQVJDOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0NIT1JEOmNhc2UgYy5HREkuUmVjb3JkVHlwZS5NRVRBX0JJVEJMVDpjYXNlIGMuR0RJLlJlY29yZFR5cGUuTUVUQV9TRVRESUJUT0RFVjpkZWZhdWx0OnZhciB1ZT0iVU5LTk9XTiI7Zm9yKHZhciBkZSBpbiBjLkdESS5SZWNvcmRUeXBlKWlmKGMuR0RJLlJlY29yZFR5cGVbZGVdPT09bil7dWU9ZGU7YnJlYWt9Yy5sb2coIltXTUZdICIrdWUrIiByZWNvcmQgKDB4IituLnRvU3RyaW5nKDE2KSsiKSBhdCBvZmZzZXQgMHgiK3MudG9TdHJpbmcoMTYpKyIgd2l0aCAiKzIqZSsiIGJ5dGVzIil9cys9MiplfSxvPXRoaXM7dDpmb3IoOyFpJiYiYnJlYWstbWFpbl9sb29wIiE9PW4oKTspO2lmKCFpKXRocm93IG5ldyBhKCJDb3VsZCBub3QgcmVhZCBhbGwgcmVjb3JkcyIpfXJldHVybiB0LnByb3RvdHlwZS5wbGF5PWZ1bmN0aW9uKHQpe2Zvcih2YXIgZT10aGlzLl9yZWNvcmRzLmxlbmd0aCxpPTA7aTxlO2krKyl0aGlzLl9yZWNvcmRzW2ldKHQpfSx0fSgpLHg9ZnVuY3Rpb24oKXtmdW5jdGlvbiB0KHQpe3RoaXMucGFyc2UodCksYy5sb2coIldNRkpTLlJlbmRlcmVyIGluc3RhbnRpYXRlZCIpfXJldHVybiB0LnByb3RvdHlwZS5yZW5kZXI9ZnVuY3Rpb24odCl7dmFyIGU9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TKCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIsInN2ZyIpO3JldHVybiB0aGlzLl9yZW5kZXIobmV3IG8oZSksdC5tYXBNb2RlLHQueEV4dCx0LnlFeHQpLGUuc2V0QXR0cmlidXRlKCJ2aWV3Qm94IixbMCwwLHQueEV4dCx0LnlFeHRdLmpvaW4oIiAiKSksZS5zZXRBdHRyaWJ1dGUoInByZXNlcnZlQXNwZWN0UmF0aW8iLCJub25lIiksZS5zZXRBdHRyaWJ1dGUoIndpZHRoIix0LndpZHRoKSxlLnNldEF0dHJpYnV0ZSgiaGVpZ2h0Iix0LmhlaWdodCksZX0sdC5wcm90b3R5cGUucGFyc2U9ZnVuY3Rpb24odCl7dGhpcy5faW1nPW51bGw7dmFyIGUsaSxzLG4sbz1uZXcgcCh0KSxyPW8ucmVhZFVpbnQzMigpO3N3aXRjaCgyNTk2NzIwMDg3PT09cj8ocz1uZXcgTChvKSxuPW8ucG9zLGU9by5yZWFkVWludDE2KCksaT1vLnJlYWRVaW50MTYoKSk6KG49MCxlPTY1NTM1JnIsaT1yPj4+MTYmNjU1MzUpLGUpe2Nhc2UgYy5HREkuTWV0YWZpbGVUeXBlLk1FTU9SWU1FVEFGSUxFOmNhc2UgYy5HREkuTWV0YWZpbGVUeXBlLkRJU0tNRVRBRklMRTppZihpPT09Yy5HREkuTUVUQUhFQURFUl9TSVpFLzIpe3ZhciBoPW8ucmVhZFVpbnQxNigpO3N3aXRjaChoKXtjYXNlIGMuR0RJLk1ldGFmaWxlVmVyc2lvbi5NRVRBVkVSU0lPTjEwMDpjYXNlIGMuR0RJLk1ldGFmaWxlVmVyc2lvbi5NRVRBVkVSU0lPTjMwMDp0aGlzLl9pbWc9bmV3IGsobyxzLGgsbisyKmkpfX19aWYobnVsbD09dGhpcy5faW1nKXRocm93IG5ldyBhKCJGb3JtYXQgbm90IHJlY29nbml6ZWQiKX0sdC5wcm90b3R5cGUuX3JlbmRlcj1mdW5jdGlvbih0LGUsaSxzKXt2YXIgbj1uZXcgTih0KTtuLnNldFZpZXdwb3J0RXh0KGkscyksbi5zZXRNYXBNb2RlKGUpLGMubG9nKCJbV01GXSBCRUdJTiBSRU5ERVJJTkcgLS0tXHgzZSIpLHRoaXMuX2ltZy5yZW5kZXIobiksYy5sb2coIltXTUZdIDwtLS0gRE9ORSBSRU5ERVJJTkciKX0sdH0oKSxCPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0KXt0aGlzLmxlZnQ9dC5yZWFkSW50MTYoKSx0aGlzLnRvcD10LnJlYWRJbnQxNigpLHRoaXMucmlnaHQ9dC5yZWFkSW50MTYoKSx0aGlzLmJvdHRvbT10LnJlYWRJbnQxNigpfXJldHVybiB0LnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiJ7bGVmdDogIit0aGlzLmxlZnQrIiwgdG9wOiAiK3RoaXMudG9wKyIsIHJpZ2h0OiAiK3RoaXMucmlnaHQrIiwgYm90dG9tOiAiK3RoaXMuYm90dG9tKyJ9In0sdH0oKSxMPWZ1bmN0aW9uKHQpe3Quc2tpcCgyKSx0aGlzLmJvdW5kaW5nQm94PW5ldyBCKHQpLHRoaXMudW5pdHNQZXJJbmNoPXQucmVhZEludDE2KCksdC5za2lwKDQpLHQuc2tpcCgyKSxjLmxvZygiR290IGJvdW5kaW5nIGJveCAiK3RoaXMuYm91bmRpbmdCb3grIiBhbmQgIit0aGlzLnVuaXRzUGVySW5jaCsiIHVuaXRzL2luY2giKX0saz1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCxlLGkscyl7dGhpcy5fdmVyc2lvbj1pLHRoaXMuX2hkcnNpemU9cyx0aGlzLl9wbGFjYWJsZT1lLHRoaXMuX3JlY29yZHM9bmV3IEcodCx0aGlzLl9oZHJzaXplKX1yZXR1cm4gdC5wcm90b3R5cGUucmVuZGVyPWZ1bmN0aW9uKHQpe3RoaXMuX3JlY29yZHMucGxheSh0KX0sdH0oKTtyZXR1cm4gZX0pKCkpKTsKLy8jIHNvdXJjZU1hcHBpbmdVUkw9V01GSlMuYnVuZGxlLm1pbi5qcy5tYXA=";
async function fd() {
  return window.WMFJS || await new Promise((a, t) => {
    const e = document.createElement("script");
    e.src = md, e.onload = a, e.onerror = t, document.head.appendChild(e);
  }), window.WMFJS;
}
const lo = 8;
class bd {
  constructor() {
    this.lWMFJS();
  }
  async lWMFJS() {
    this.WMFJS || (this.WMFJS = await fd(), this.WMFJS.loggingEnabled(!1));
  }
  async render(t, e = { dpi: 96, rasterScale: 2 }) {
    var u;
    const s = (e == null ? void 0 : e.dpi) ?? 96, i = (e == null ? void 0 : e.rasterScale) ?? 1, n = await this.renderWmfToSvgElement(t, s);
    document.body.appendChild(n);
    const r = ((u = n.getAttribute("viewBox")) == null ? void 0 : u.split(/\s+/).map(Number)) || [0, 0, 512, 512], o = r[2] || 512, l = r[3] || 512, c = await this.rasterizeSvgToCanvas(n, Math.round(o * i), Math.round(l * i), (e == null ? void 0 : e.fillWhite) ?? !0);
    return new nu(c);
  }
  async rasterizeSvgToCanvas(t, e, s, i = !0) {
    const n = new XMLSerializer().serializeToString(t), r = new Blob([n], { type: "image/svg+xml" }), o = URL.createObjectURL(r), l = await this._loadImg(o);
    let c = e, h = s;
    if (!c || !h) {
      const p = /viewBox="([\d.\s-]+)"/.exec(n);
      if (p) {
        const [, m] = p, [, , f, b] = m.trim().split(/\s+/).map(Number);
        c || (c = Math.max(1, Math.round(f))), h || (h = Math.max(1, Math.round(b)));
      } else
        c = c || l.naturalWidth || 1024, h = h || l.naturalHeight || 1024;
    }
    const u = document.createElement("canvas");
    u.width = c, u.height = h;
    const d = u.getContext("2d");
    return i && (d.fillStyle = "#fff", d.fillRect(0, 0, u.width, u.height)), d.drawImage(l, 0, 0, u.width, u.height), URL.revokeObjectURL(o), u;
  }
  async _loadImg(t) {
    return new Promise((e, s) => {
      const i = new Image();
      i.onload = () => {
        e(i);
      }, i.onerror = (n) => {
        s(n);
      }, i.src = t;
    });
  }
  async renderWmfToSvgElement(t, e = 96) {
    await this.lWMFJS();
    const s = new this.WMFJS.Renderer(t), i = this.getIRendererSettingsFromWMF(t, { dpi: e });
    return s.render(i);
  }
  getIRendererSettingsFromWMF(t, e) {
    var u, d;
    const s = (e == null ? void 0 : e.dpi) ?? 96, i = t instanceof Uint8Array ? new DataView(t.buffer, t.byteOffset, t.byteLength) : new DataView(t);
    let n = 0;
    const r = 2596720087;
    let o = !1;
    const l = Math.min(i.byteLength - 22, 256);
    for (let p = 0; p <= l; p += 2)
      if (i.getUint32(p, !0) === r) {
        n = p, o = !0;
        break;
      }
    if (o) {
      const p = i.getInt16(n + 6, !0), m = i.getInt16(n + 8, !0), f = i.getInt16(n + 10, !0), b = i.getInt16(n + 12, !0), y = i.getInt16(n + 14, !0), x = Math.max(1, f - p), g = Math.max(1, b - m), S = Math.max(1, Math.round(x / y * s)), Z = Math.max(1, Math.round(g / y * s));
      return {
        width: `${S}px`,
        height: `${Z}px`,
        xExt: x,
        yExt: g,
        mapMode: lo
      };
    }
    const c = ((u = e == null ? void 0 : e.fallbackPx) == null ? void 0 : u.width) ?? 512, h = ((d = e == null ? void 0 : e.fallbackPx) == null ? void 0 : d.height) ?? 512;
    return {
      width: `${c}px`,
      height: `${h}px`,
      xExt: c,
      yExt: h,
      mapMode: lo
    };
  }
}
class yd {
  constructor() {
    this.cfbSig = [208, 207, 17, 224, 161, 177, 26, 225];
  }
  getImageData(t) {
    const e = {
      type: "",
      content: null
    }, s = this._indexOfSeq(t, this.cfbSig);
    if (s >= 0 && (t = t.slice(s)), this._isCFB(t)) {
      const i = this._findEmbeddedPreview(t);
      i ? (e.type = i.type, e.content = i.content, e.start = i.start, e.end = i.end) : e.error = "OLE container found, but no recognizable image/metafile stream was located.";
    }
    return e;
  }
  _indexOfSeq(t, e) {
    t: for (let s = 0; s <= t.length - e.length; s++) {
      for (let i = 0; i < e.length; i++) if (t[s + i] !== e[i]) continue t;
      return s;
    }
    return -1;
  }
  _isCFB(t) {
    return t.length >= 8 && t[0] === this.cfbSig[0] && t[1] === this.cfbSig[1] && t[2] === this.cfbSig[2] && t[3] === this.cfbSig[3] && t[4] === this.cfbSig[4] && t[5] === this.cfbSig[5] && t[6] === this.cfbSig[6] && t[7] === this.cfbSig[7];
  }
  _findEmbeddedPreview(t) {
    const e = this._findJpeg(t);
    if (e) return e;
    const s = this._findGif(t);
    if (s) return s;
    const i = this._findBmp(t);
    if (i) return i;
    const n = this._findWmf(t);
    if (n) return n;
    const r = this._findPng(t);
    if (r) return r;
    const o = this._findEmf(t);
    if (o) return o;
    const l = this._findDib(t);
    return l || null;
  }
  _findPng(t) {
    const e = [137, 80, 78, 71, 13, 10, 26, 10], s = this._indexOfSeq(t, e);
    if (s < 0) return null;
    let i = s + 8;
    for (; i + 12 <= t.length; ) {
      const n = this._readU32BE(t, i), r = i + 12 + n;
      if (r > t.length) break;
      if (t[i + 4] === 73 && t[i + 5] === 69 && t[i + 6] === 78 && t[i + 7] === 68) return { type: "image/png", start: s, end: r, content: t.slice(s, r) };
      i = r;
    }
    return null;
  }
  _findJpeg(t) {
    const e = this._indexOfSeq(t, [255, 216, 255]);
    if (e < 0) return null;
    for (let s = e + 2; s < t.length - 1; s++)
      if (t[s] === 255 && t[s + 1] === 217) {
        const i = s + 2;
        return { type: "image/jpeg", start: e, end: i, content: t.slice(e, i) };
      }
    return null;
  }
  _findGif(t) {
    let e = this._indexOfSeq(t, [71, 73, 70, 56, 57, 97]);
    if (e < 0 && (e = this._indexOfSeq(t, [71, 73, 70, 56, 55, 97])), e < 0) return null;
    for (let s = e + 6; s < t.length; s++)
      if (t[s] === 59) {
        const i = s + 1;
        return { type: "image/gif", start: e, end: i, content: t.slice(e, i) };
      }
    return null;
  }
  _findBmp(t) {
    const e = this._indexOfSeq(t, [66, 77]);
    if (e < 0 || e + 6 > t.length) return null;
    const s = this._readU32LE(t, e + 2);
    if (s > 0 && e + s <= t.length) {
      const i = e + s;
      return { type: "image/bmp", start: e, end: i, content: t.slice(e, i) };
    }
    return null;
  }
  _findWmf(t) {
    const e = [215, 205, 198, 154], s = this._indexOfSeq(t, e);
    if (s < 0) return null;
    const i = s + 22;
    if (i + 8 > t.length) return null;
    this._readU16LE(t, i + 2);
    const r = this._readU32LE(t, i + 4) * 2;
    if (r <= 0) return null;
    const o = s + r;
    return o <= t.length ? { type: "wmf", start: s, end: o, content: t.slice(s, o) } : { type: "wmf", start: s, end: t.length, content: t.slice(s) };
  }
  _findEmf(t) {
    for (let e = 0; e + 44 <= t.length; e++)
      if (t[e + 40] === 32 && t[e + 41] === 69 && t[e + 42] === 77 && t[e + 43] === 70) {
        const s = e;
        if (s < 40) continue;
        const i = s;
        if (i + 52 > t.length) continue;
        const n = this._readU32LE(t, i + 48);
        if (n > 0 && i + n <= t.length) {
          const r = i + n;
          return { type: "emf", start: i, end: r, content: t.slice(i, r) };
        }
      }
    return null;
  }
  _findDib(t) {
    for (let e = 0; e + 40 <= t.length; e++) {
      if (this._readU32LE(t, e) !== 40) continue;
      const i = this._readS32LE(t, e + 4), n = this._readS32LE(t, e + 8), r = this._readU16LE(t, e + 12), o = this._readU16LE(t, e + 14), l = this._readU32LE(t, e + 16), c = this._readU32LE(t, e + 20);
      if (r !== 1 || i <= 0 || Math.abs(n) <= 0 || ![1, 4, 8, 16, 24, 32].includes(o)) continue;
      let h = 0;
      if (o <= 8) {
        const m = this._readU32LE(t, e + 32);
        h = (m || 1 << o) * 4;
      } else l === 3 && (h = 12);
      let u = c;
      if (u === 0 && l === 0) {
        const m = o / 8;
        u = Math.ceil(i * m / 4) * 4 * Math.abs(n);
      }
      const d = 40 + h + (u || 0), p = e + d;
      if (d > 40 && p <= t.length)
        return { type: "image/dib", start: e, end: p, content: t.slice(e, p) };
    }
    return null;
  }
  _readU16LE(t, e) {
    return t[e] | t[e + 1] << 8;
  }
  _readU32LE(t, e) {
    return t[e] | t[e + 1] << 8 | t[e + 2] << 16 | t[e + 3] << 24;
  }
  _readS32LE(t, e) {
    return this._readU32LE(t, e) | 0;
  }
  _readU32BE(t, e) {
    return t[e] << 24 | t[e + 1] << 16 | t[e + 2] << 8 | t[e + 3];
  }
}
class Wc extends Gt {
  constructor(t) {
    super(t), this._Ole2FrameCFBHelpers = new yd(), this._wmfRenderer = new bd();
  }
  /**
   * It filters all the line, polyline and lwpolylin entities and draw them.
   * @param data {DXFData} dxf parsed data.
   * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  async draw(t) {
    let e = new at();
    e.name = "OLE2FRAMES";
    let s = t.entities.filter((i) => i.type === "OLE2FRAME");
    if (s.length === 0) return null;
    for (let i = 0; i < s.length; i++) {
      let n = s[i];
      if (this._hideEntity(n)) continue;
      let r = this._getCached(n), o = null, l = null;
      if (r)
        o = r.geometry, l = r.material;
      else {
        let h = await this.drawOle2Frame(n);
        o = h.geometry, l = h.material, l.side = 2, this._setCache(n, h);
      }
      let c = new gt(o, l);
      c.userData = { entity: n }, e.add(c);
    }
    return e;
  }
  /**
   * Draws an Ole2Frame entity.
   * @param entity {entity} dxf parsed Ole2Frame entity.
   * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  async drawOle2Frame(t) {
    const e = t.lowerRightX - t.upperLeftX, s = t.upperLeftY - t.lowerRightY;
    let i = new Pr(e, s);
    i.translate(t.upperLeftX + e / 2, t.upperLeftY - s / 2, 0);
    let n = new Iu();
    return n.map = await this._createTextureFromImageData(t), n.color.setHex(16777215), n.color.convertSRGBToLinear(), { geometry: i, material: n };
  }
  async _createTextureFromImageData(t) {
    let e = this.hexToUint8Array(t.data);
    Number.isFinite(t.length) && t.length > 0 && e.length > t.length && (e = e.slice(0, t.length));
    const s = this._Ole2FrameCFBHelpers.getImageData(e);
    if (/wmf/.test(s.type))
      return await this._wmfRenderer.render(s.content);
    if (/emf/.test(s.type))
      return this.trigger("log", `Unsupported image type (mime=${s.type}).`), null;
    if (!s.type.startsWith("image/"))
      return this.trigger("log", `Unsupported image type (mime=${s.type}).`), null;
    const i = new Blob([s.content], { type: s.type }), n = URL.createObjectURL(i), r = new It();
    return await new Promise((o, l) => {
      const c = new Image();
      c.onload = () => {
        r.image = c, r.needsUpdate = !0, URL.revokeObjectURL(n), o();
      }, c.onerror = () => {
        URL.revokeObjectURL(n), l(new Error(`Failed to decode image (${s.type}) from OLE payload`));
      }, c.src = n;
    }), r;
  }
  hexToUint8Array(t) {
    const e = (t || "").replace(/\s+/g, "");
    if (e.length % 2) throw new Error("Hex string has odd length.");
    const s = new Uint8Array(e.length >>> 1);
    for (let i = 0, n = 0; i < e.length; i += 2, n++) s[n] = parseInt(e.substr(i, 2), 16);
    return s;
  }
}
function co(a, t = !1) {
  const e = a[0].index !== null, s = new Set(Object.keys(a[0].attributes)), i = new Set(Object.keys(a[0].morphAttributes)), n = {}, r = {}, o = a[0].morphTargetsRelative, l = new rt();
  let c = 0;
  for (let h = 0; h < a.length; ++h) {
    const u = a[h];
    let d = 0;
    if (e !== (u.index !== null))
      return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + ". All geometries must have compatible attributes; make sure index attribute exists among all geometries, or in none of them."), null;
    for (const p in u.attributes) {
      if (!s.has(p))
        return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + '. All geometries must have compatible attributes; make sure "' + p + '" attribute exists among all geometries, or in none of them.'), null;
      n[p] === void 0 && (n[p] = []), n[p].push(u.attributes[p]), d++;
    }
    if (d !== s.size)
      return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + ". Make sure all geometries have the same number of attributes."), null;
    if (o !== u.morphTargetsRelative)
      return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + ". .morphTargetsRelative must be consistent throughout all geometries."), null;
    for (const p in u.morphAttributes) {
      if (!i.has(p))
        return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + ".  .morphAttributes must be consistent throughout all geometries."), null;
      r[p] === void 0 && (r[p] = []), r[p].push(u.morphAttributes[p]);
    }
    if (t) {
      let p;
      if (e)
        p = u.index.count;
      else if (u.attributes.position !== void 0)
        p = u.attributes.position.count;
      else
        return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed with geometry at index " + h + ". The geometry must have either an index or a position attribute"), null;
      l.addGroup(c, p, h), c += p;
    }
  }
  if (e) {
    let h = 0;
    const u = [];
    for (let d = 0; d < a.length; ++d) {
      const p = a[d].index;
      for (let m = 0; m < p.count; ++m)
        u.push(p.getX(m) + h);
      h += a[d].attributes.position.count;
    }
    l.setIndex(u);
  }
  for (const h in n) {
    const u = ho(n[h]);
    if (!u)
      return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed while trying to merge the " + h + " attribute."), null;
    l.setAttribute(h, u);
  }
  for (const h in r) {
    const u = r[h][0].length;
    if (u === 0) break;
    l.morphAttributes = l.morphAttributes || {}, l.morphAttributes[h] = [];
    for (let d = 0; d < u; ++d) {
      const p = [];
      for (let f = 0; f < r[h].length; ++f)
        p.push(r[h][f][d]);
      const m = ho(p);
      if (!m)
        return console.error("THREE.BufferGeometryUtils: .mergeGeometries() failed while trying to merge the " + h + " morphAttribute."), null;
      l.morphAttributes[h].push(m);
    }
  }
  return l;
}
function ho(a) {
  let t, e, s, i = -1, n = 0;
  for (let c = 0; c < a.length; ++c) {
    const h = a[c];
    if (t === void 0 && (t = h.array.constructor), t !== h.array.constructor)
      return console.error("THREE.BufferGeometryUtils: .mergeAttributes() failed. BufferAttribute.array must be of consistent array types across matching attributes."), null;
    if (e === void 0 && (e = h.itemSize), e !== h.itemSize)
      return console.error("THREE.BufferGeometryUtils: .mergeAttributes() failed. BufferAttribute.itemSize must be consistent across matching attributes."), null;
    if (s === void 0 && (s = h.normalized), s !== h.normalized)
      return console.error("THREE.BufferGeometryUtils: .mergeAttributes() failed. BufferAttribute.normalized must be consistent across matching attributes."), null;
    if (i === -1 && (i = h.gpuType), i !== h.gpuType)
      return console.error("THREE.BufferGeometryUtils: .mergeAttributes() failed. BufferAttribute.gpuType must be consistent across matching attributes."), null;
    n += h.count * e;
  }
  const r = new t(n), o = new q(r, e, s);
  let l = 0;
  for (let c = 0; c < a.length; ++c) {
    const h = a[c];
    if (h.isInterleavedBufferAttribute) {
      const u = l / e;
      for (let d = 0, p = h.count; d < p; d++)
        for (let m = 0; m < e; m++) {
          const f = h.getComponent(d, m);
          o.setComponent(d + u, m, f);
        }
    } else
      r.set(h.array, l);
    l += h.count * e;
  }
  return i !== void 0 && (o.gpuType = i), o;
}
class xd {
  constructor() {
  }
  /**
   * Returns a merged scene. Setting clone to true avoids changing original geometry, but its permormance is lower
   * @param scene {THREE.Object3D} Threejs object (usually the scene itself).
   * @param clone {boolean} Flag to change cloning of scene.
   * @param uuids {Array} Array of uuids to merge only specific objects.
      * @return {THREE.Object3D} object with merged data
  */
  merge(t, e = !0, s = []) {
    let { mesh: i, line: n } = this._getMergedObjects(t, e, s);
    if ((i || n) && t.userData && t.userData.entity) {
      let r = t.userData.entity.scaleX ? t.userData.entity.scaleX : 1, o = t.userData.entity.scaleY ? t.userData.entity.scaleY : 1, l = t.userData.entity.scaleZ ? t.userData.entity.scaleZ : 1;
      i && (i.scale.set(r, o, l), t.userData.entity.rotation && (i.rotation.z = t.userData.entity.rotation * Math.PI / 180), i.position.set(t.userData.entity.x, t.userData.entity.y, t.userData.entity.z), t.add(i)), n && (n.scale.set(r, o, l), t.userData.entity.rotation && (n.rotation.z = t.userData.entity.rotation * Math.PI / 180), n.position.set(t.userData.entity.x, t.userData.entity.y, t.userData.entity.z), t.add(n));
    } else
      i && t.add(i), n && t.add(n);
    return this._removeEmptyGroups(t), t;
  }
  _getMergedObjects(t, e, s) {
    let { mesh: i, line: n } = this._getMergedGeometry(t, e, s);
    const r = i.geometry ? new gt(i.geometry, i.materials) : null, o = n.geometry ? new Ts(n.geometry, n.materials) : null;
    return r && (r.userData = { entities: i.userData, entity: t.userData.entity }), o && (o.userData = { entities: n.userData, entity: t.userData.entity }), { mesh: r, line: o };
  }
  _getMergedGeometry(t, e, s) {
    let i = [], n = [], r = [], o = [], l = null;
    t.updateWorldMatrix(!1, !0);
    let c = {};
    s.forEach((d) => c[d] = !0), this._removableTraverse(t, (d) => {
      if (!(d.isMesh || d.isLineSegments || d.isLine) || s.length > 0 && !c[d.uuid]) return;
      let p = e ? d.geometry.clone() : d.geometry;
      if (p.applyMatrix4(d.matrixWorld), d.isMesh) {
        p.index && (p = p.toNonIndexed()), p.hasAttribute("normal") || p.computeVertexNormals();
        let b = p.getAttribute("uv");
        if (b || (b = new q(new Float32Array(p.attributes.position.count * 2), 2), p.setAttribute("uv", b)), p.attributes.uv.itemSize > 2) {
          let y = new q(new Float32Array(p.attributes.uv.count * 2), 2);
          for (let x = 0; x < p.attributes.uv.count; x++)
            y.set(p.attributes.uv.getX(x), x * 2), y.set(p.attributes.uv.getY(x), x * 2 + 1);
          p.deleteAttribute("uv"), p.setAttribute("uv", y);
        }
        l = r;
      } else
        p = d.geometry.clone(), p.applyMatrix4(d.matrixWorld), p.index || this._indexLineGeometry(p), p.deleteAttribute("lineDistance"), p.deleteAttribute("uv"), p.deleteAttribute("normal"), l = o;
      l.push({ uuid: d.uuid, userData: d.userData });
      let m = d.isMesh ? i : n, f = this._findGroup(m, d.material);
      f || (f = {
        material: d.material,
        geometries: []
      }, m.push(f)), f.geometries.push(p), p.ent = d, d.parent && d.parent.remove(d);
    });
    let h = i.length > 0 ? this._mergeByMaterial(i) : null, u = n.length > 0 ? this._mergeByMaterial(n) : null;
    return {
      mesh: {
        geometry: h ? h.geometry : null,
        materials: h ? h.materials : null,
        userData: r
      },
      line: {
        geometry: u ? u.geometry : null,
        materials: u ? u.materials : null,
        userData: o
      }
    };
  }
  _mergeByMaterial(t) {
    let e = t.map((i) => i.material);
    return e = e.flat(), {
      geometry: co(
        t.map((i) => co(i.geometries, !1)),
        !0
      ),
      materials: e
    };
  }
  _removeEmptyGroups(t) {
    for (let e = t.children.length - 1; e > -1; e--) {
      let s = t.children[e];
      this._removeEmptyGroups(s), s.isGroup && s.children.length === 0 && t.remove(s);
    }
  }
  _indexLineGeometry(t) {
    let e = [], s = t.attributes.position.count;
    for (let i = 0; i < s; i++)
      i > 0 && (e.push(i - 1), e.push(i));
    t.index || t.setIndex(new q(new Uint16Array(e), 1));
  }
  //USE THIS INSTEAD OF Array.find: FOR BETTER PERFORMANCE
  _findGroup(t, e) {
    for (let s = 0; s < t.length; s++)
      if (t[s].material === e) return t[s];
    return null;
  }
  _removableTraverse(t, e) {
    for (let s = t.children.length - 1; s > -1; s--)
      this._removableTraverse(t.children[s], e);
    e(t);
  }
}
class gd extends xd {
  constructor() {
    super();
  }
  // @override
  merge(t, e = !0, s = []) {
    let { mesh: i, line: n } = this._getMergedObjects(t, e, s);
    if ((i || n) && t.userData && t.userData.entity) {
      let r = t.userData.entity.scaleX ? t.userData.entity.scaleX : 1, o = t.userData.entity.scaleY ? t.userData.entity.scaleY : 1, l = t.userData.entity.scaleZ ? t.userData.entity.scaleZ : 1;
      i && (i.scale.set(r, o, l), t.userData.entity.rotation && (i.rotation.z = t.userData.entity.rotation * Math.PI / 180), i.position.set(t.userData.entity.x, t.userData.entity.y, t.userData.entity.z), t.add(i)), n && (n.scale.set(r, o, l), t.userData.entity.rotation && (n.rotation.z = t.userData.entity.rotation * Math.PI / 180), n.position.set(t.userData.entity.x, t.userData.entity.y, t.userData.entity.z), t.add(n));
    } else
      i && i.add(i), n && t.add(n);
    return this._removeEmptyGroups(t), t;
  }
  // @override 
  _getMergedObjects(t, e, s) {
    let { mesh: i, line: n } = this._getMergedGeometry(t, e, s);
    const r = i.geometry ? new gt(i.geometry, i.materials) : null, o = n.geometry ? new Ts(n.geometry, n.materials) : null;
    return r && (r.userData = { entities: i.userData, entity: t.userData.entity }), o && (o.userData = { entities: n.userData, entity: t.userData.entity }), { mesh: r, line: o };
  }
}
class Ic extends Gt {
  constructor(t, e) {
    super(t), this._font = e, this._lineEntity = new Is(t, e), this._textEntity = new zt(t, e), this._solidEntity = new Qr(t), this._circleEntity = new Xc(t), this._splineEntity = new jr(t), this._hatchEntity = new zc(t), this._ole2FrameEntity = new Wc(t);
  }
  /**
   * Draws the block entity.
   * @param entity {Entity} DXF block entity.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  async drawBlock(t, e = 1) {
    let s = new at();
    s.name = "BLOCK", s.position.set(-t.x, -t.y, -t.z), s.userData = {
      entity: t,
      entities: []
    };
    const i = t.entities.filter((o) => o.type === "HATCH"), n = t.entities.filter((o) => o.type !== "HATCH");
    for (let o = 0; o < n.length; o++) {
      let l = n[o];
      if (this._hideEntity(l)) continue;
      const c = await this._generateBlock3d(l, e);
      c && s.add(c);
    }
    const r = (o) => s.children.filter((l) => o.find((c) => c === l.userData.entity.handle));
    for (let o = 0; o < i.length; o++) {
      let l = i[o];
      if (this._hideEntity(l)) continue;
      const c = await this._generateBlock3d(l, e, r);
      c && s.add(c);
    }
    return this._mergeGroup(s), s;
  }
  async _generateBlock3d(t, e, s) {
    switch (t.type) {
      case "LINE": {
        let i = this._lineEntity.drawLine(t, e), n = new Xt(i.geometry, i.material);
        return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n;
      }
      case "POLYLINE":
      case "LWPOLYLINE": {
        let i = this._lineEntity.drawPolyLine(t, e), n = new Xt(i.geometry, i.material);
        return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n;
      }
      case "ARC":
      case "CIRCLE": {
        let i = this._circleEntity.drawCircle(t, e), n = new Xt(i.geometry, i.material);
        return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n;
      }
      case "ELLIPSE": {
        let i = this._circleEntity.drawEllipse(t, e), n = new Xt(i.geometry, i.material);
        return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n;
      }
      case "SPLINE": {
        let i = this._splineEntity.drawSpline(t, e), n = new Xt(i.geometry, i.material);
        return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n;
      }
      case "SOLID": {
        let i = this._solidEntity.drawSolid(t, e), n = new gt(i.geometry, i.material);
        return n.userData = { entity: t }, n;
      }
      case "ATTRIB":
      case "TEXT":
      case "MTEXT": {
        let i = this._textEntity.drawText(t, e);
        if (!i) return null;
        let n = new gt(i.geometry, i.material);
        return n.userData = { entity: t }, n;
      }
      case "INSERT":
        {
          let i = t.blockObj ? t.blockObj : this._getBlock(this.data.blocks, t.block);
          if (i && i.entities.length > 0 && !this._hideBlockEntity(i)) {
            let n = new at();
            n.name = "INSERT", n.add(await this.drawBlock(i, e)), n.userData = { entity: t };
            let r = t.scaleX ? t.scaleX : 1, o = t.scaleY ? t.scaleY : 1, l = t.scaleZ ? t.scaleZ : 1;
            return t.extrusionZ = t.extrusionZ < 0 ? -1 : 1, n.scale.set(t.extrusionZ * r, o, l), t.rotation && (n.rotation.z = t.extrusionZ * t.rotation * Math.PI / 180), n.position.set(t.extrusionZ * t.x, t.y, t.z), n;
          }
        }
        break;
      case "HATCH":
        {
          let i = this._hatchEntity.drawHatch(t, s);
          if (i.geometry && i.geometry.attributes.position.count > 0) {
            let n = t.fillType === "SOLID" ? new gt(i.geometry, i.material) : new Ts(i.geometry, i.material);
            return i.material.type === "LineDashedMaterial" && this._geometryHelper.fixMeshToDrawDashedLines(n), n.userData = { entity: t }, n.renderOrder = t.fillType === "SOLID" ? -1 : 0, n.position.z = t.fillType === "SOLID" ? -0.1 : 0, n;
          }
        }
        break;
      case "POINT":
        break;
      case "ATTDEF":
        break;
      case "DIMENSION":
        break;
      case "OLE2FRAME": {
        let i = await this._ole2FrameEntity.drawOle2Frame(t, e), n = new gt(i.geometry, i.material);
        return n.userData = { entity: t }, n;
      }
      default:
        this.trigger("log", "unknown entity type: " + t.type);
        break;
    }
    return null;
  }
  _rotate(t, e) {
    !e || e === 0 || t.rotateOnAxis(this._geometryHelper.zAxis, e * Math.PI / 180);
  }
  _hideBlockEntity(t) {
    return t && t.name.toLowerCase().startsWith("*paper_space") ? !0 : this._hideEntity(t);
  }
  _mergeGroup(t) {
    const e = new gd(), s = (r) => r.name === "BLOCK" || r.name === "INSERT" || r.name === "DIMENSION", i = [], n = [];
    for (let r = 0; r < t.children.length; r++) {
      const o = t.children[r];
      s(o) ? i.push(o) : n.push(o);
    }
    for (let r = 0; r < i.length; r++) t.remove(i[r]);
    n.length > 0 && (t.userData.entities = [], n.forEach((r) => t.userData.entities.push(r)), e.merge(t));
    for (let r = 0; r < i.length; r++) t.add(i[r]);
  }
}
class Cc extends Gt {
  constructor(t, e) {
    super(t), this._font = e, this._blockEntity = new Ic(t, e);
  }
  /**
   * It filters all the insert entities and draw them. Uses blockEntity.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  async draw(t) {
    this.data = t;
    let e = t.entities.filter((i) => i.type === "INSERT");
    if (e.length === 0) return null;
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (typeof n.blockObj < "u") continue;
      let r = this._getBlock(this.data.blocks, n.block);
      r && (n.blockObj = r);
    }
    let s = new at();
    s.name = "INSERTS";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (this._hideEntity(n)) continue;
      let r = await this.drawInsert(n);
      r && s.add(r);
    }
    return s;
  }
  /**
   * Draws an insert entity.
   * @param entity {entity} dxf parsed insert entity.
      * @return {Object} object composed as {geometry: THREE.Geometry, material: THREE.Material}
  */
  async drawInsert(t) {
    let e = this._getCached(t);
    if (e)
      return e;
    let s = typeof t.scaleX < "u" ? t.scaleX : 1, i = typeof t.scaleY < "u" ? t.scaleY : 1, n = typeof t.scaleZ < "u" ? t.scaleZ : 1, r = t.blockObj ? t.blockObj : this._getBlock(this.data.blocks, t.block), o = null;
    if (r && !this._blockEntity._hideBlockEntity(r)) {
      o = new at(), o.name = "INSERT", o.userData = { entity: t };
      const l = t.extrusionZ < 0 ? -1 : 1;
      o.add(await this._blockEntity.drawBlock(r, l)), o.scale.set(l * s, i, n), t.rotation && (o.rotation.z = l * (t.rotation * Math.PI / 180)), o.position.set(l * t.x, t.y, t.z);
    }
    return this._setCache(t, o), o;
  }
}
class Rd extends Gt {
  constructor(t, e) {
    super(t), this._font = e, this._lineEntity = new Is(t, e), this._textEntity = new zt(t, e), this._insertEntity = new Cc(t, e), this._solidEntity = new Qr(t), this._blockEntity = new Ic(t, e);
  }
  /**
   * It filters all the dimension entities and draw them. Uses Line, Text, Insert, Solid & Block entities.
   * @param data {DXFData} dxf parsed data.
      * @return {THREE.Group} ThreeJS object with all the generated geometry. DXF entity is added into userData
  */
  async draw(t) {
    let e = t.entities.filter((i) => i.type === "DIMENSION");
    if (e.length === 0) return null;
    for (let i = 0; i < e.length; i++) {
      let n = e[i], r = this._getBlock(t.blocks, n.block);
      r && (n.blockObj = r);
    }
    let s = new at();
    s.name = "DIMENSIONS";
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (!n.blockObj) continue;
      let r = new at();
      r.name = "DIMENSION", r.userData = { entity: n };
      let o = await this._blockEntity.drawBlock(n.blockObj);
      r.add(o), s.add(r);
    }
    return s;
  }
}
function Sd() {
}
function Zd() {
}
function Gd() {
  console.error.apply(void 0, arguments);
}
const ie = {
  info: Sd,
  warn: Zd,
  error: Gd
}, Vd = (a) => {
  let t;
  const e = {};
  return a.forEach((s) => {
    const i = s[0], n = s[1];
    switch (n) {
      case "$MEASUREMENT": {
        t = "measurement";
        break;
      }
      case "$INSUNITS": {
        t = "insUnits";
        break;
      }
      case "$EXTMIN":
        e.extMin = {}, t = "extMin";
        break;
      case "$EXTMAX":
        e.extMax = {}, t = "extMax";
        break;
      case "$DIMASZ":
        e.dimArrowSize = {}, t = "dimArrowSize";
        break;
      default:
        switch (t) {
          case "extMin":
          case "extMax": {
            switch (i) {
              case 10:
                e[t].x = n;
                break;
              case 20:
                e[t].y = n;
                break;
              case 30:
                e[t].z = n, t = void 0;
                break;
            }
            break;
          }
          case "measurement":
          case "insUnits": {
            switch (i) {
              case 70: {
                e[t] = n, t = void 0;
                break;
              }
            }
            break;
          }
          case "dimArrowSize": {
            switch (i) {
              case 40: {
                e[t] = n, t = void 0;
                break;
              }
            }
            break;
          }
        }
    }
  }), e;
}, Md = (a) => {
  let t = null, e = null;
  return a.reduce(
    (s, i) => {
      const n = i[0], r = i[1];
      switch (n) {
        case 2:
          s.name = r;
          break;
        case 3:
          s.description = r;
          break;
        case 70:
          s.flag = r;
          break;
        case 72:
          s.alignment = r;
          break;
        case 73:
          s.elementCount = parseInt(r);
          break;
        case 40:
          s.patternLength = r;
          break;
        case 49:
          t = /* @__PURE__ */ Object.create({ scales: [], offset: [] }), t.length = r, s.pattern.push(t);
          break;
        case 74:
          t.shape = r;
          break;
        case 75:
          t.shapeNumber = r;
          break;
        case 340:
          t.styleHandle = r;
          break;
        case 46:
          t.scales.push(r);
          break;
        case 50:
          t.rotation = r;
          break;
        case 44:
          e = /* @__PURE__ */ Object.create({ x: r, y: 0 }), t.offset.push(e);
          break;
        case 45:
          e.y = r;
          break;
        case 9:
          t.text = r;
          break;
      }
      return s;
    },
    { type: "LTYPE", pattern: [] }
  );
}, Ld = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 2:
        t.name = i;
        break;
      case 6:
        t.lineTypeName = i;
        break;
      case 62:
        t.colorNumber = i;
        break;
      case 70:
        t.flags = i;
        break;
      case 290:
        t.plot = parseInt(i) !== 0;
        break;
      case 370:
        t.lineWeightEnum = i;
        break;
    }
    return t;
  },
  { type: "LAYER" }
), wd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 2:
        t.name = i;
        break;
      case 6:
        t.lineTypeName = i;
        break;
      case 40:
        t.fixedTextHeight = i;
        break;
      case 41:
        t.widthFactor = i;
        break;
      case 50:
        t.obliqueAngle = i;
        break;
      case 71:
        t.flags = i;
        break;
      case 42:
        t.lastHeightUsed = i;
        break;
      case 3:
        t.primaryFontFileName = i;
        break;
      case 4:
        t.bigFontFileName = i;
        break;
    }
    return t;
  },
  { type: "STYLE" }
), vd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 2:
        t.name = i;
        break;
      case 5:
        t.handle = i;
        break;
      case 70:
        t.flags = i;
        break;
      case 10:
        t.lowerLeft.x = parseFloat(i);
        break;
      case 20:
        t.lowerLeft.y = parseFloat(i);
        break;
      case 11:
        t.upperRight.x = parseFloat(i);
        break;
      case 21:
        t.upperRight.y = parseFloat(i);
        break;
      case 12:
        t.center.x = parseFloat(i);
        break;
      case 22:
        t.center.y = parseFloat(i);
        break;
      case 14:
        t.snapSpacing.x = parseFloat(i);
        break;
      case 24:
        t.snapSpacing.y = parseFloat(i);
        break;
      case 15:
        t.gridSpacing.x = parseFloat(i);
        break;
      case 25:
        t.gridSpacing.y = parseFloat(i);
        break;
      case 16:
        t.direction.x = parseFloat(i);
        break;
      case 26:
        t.direction.y = parseFloat(i);
        break;
      case 36:
        t.direction.z = parseFloat(i);
        break;
      case 17:
        t.target.x = parseFloat(i);
        break;
      case 27:
        t.target.y = parseFloat(i);
        break;
      case 37:
        t.target.z = parseFloat(i);
        break;
      case 45:
        t.height = parseFloat(i);
        break;
      case 50:
        t.snapAngle = parseFloat(i);
        break;
      case 51:
        t.angle = parseFloat(i);
        break;
      case 110:
        t.x = parseFloat(i);
        break;
      case 120:
        t.y = parseFloat(i);
        break;
      case 130:
        t.z = parseFloat(i);
        break;
      case 111:
        t.xAxisX = parseFloat(i);
        break;
      case 121:
        t.xAxisY = parseFloat(i);
        break;
      case 131:
        t.xAxisZ = parseFloat(i);
        break;
      case 112:
        t.xAxisX = parseFloat(i);
        break;
      case 122:
        t.xAxisY = parseFloat(i);
        break;
      case 132:
        t.xAxisZ = parseFloat(i);
        break;
      case 146:
        t.elevation = parseFloat(i);
        break;
    }
    return t;
  },
  {
    type: "VPORT",
    center: {},
    lowerLeft: {},
    upperRight: {},
    snap: {},
    snapSpacing: {},
    gridSpacing: {},
    direction: {},
    target: {}
  }
), Vs = (a, t, e) => {
  const s = [];
  let i;
  return a.forEach((n) => {
    const r = n[0], o = n[1];
    (r === 0 || r === 2) && o === t ? (i = [], s.push(i)) : i.push(n);
  }), s.reduce((n, r) => {
    const o = e(r);
    return o.name && (n[o.name] = o), n;
  }, {});
}, Fd = (a) => {
  const t = [];
  let e;
  a.forEach((o) => {
    const l = o[1];
    l === "TABLE" ? (e = [], t.push(e)) : l === "ENDTAB" ? t.push(e) : e.push(o);
  });
  let s = [], i = [], n = [], r = [];
  return t.forEach((o) => {
    o[0][1] === "STYLE" ? s = o : o[0][1] === "LTYPE" ? r = o : o[0][1] === "LAYER" ? i = o : o[0][1] === "VPORT" && (n = o);
  }), {
    layers: Vs(i, "LAYER", Ld),
    styles: Vs(s, "STYLE", wd),
    vports: Vs(n, "VPORT", vd),
    ltypes: Vs(r, "LTYPE", Md)
  };
}, D = (a, t) => {
  switch (a) {
    case 5:
      return {
        handle: t
      };
    case 6:
      return {
        lineTypeName: t
      };
    case 8:
      return {
        layer: t
      };
    case 48:
      return {
        lineTypeScale: t
      };
    case 60:
      return {
        visible: t === 0
      };
    case 62:
      return {
        colorNumber: t
      };
    case 67:
      return t === 0 ? {} : {
        paperSpace: t
      };
    case 68:
      return {
        viewportOn: t
      };
    case 69:
      return {
        viewport: t
      };
    case 210:
      return {
        extrusionX: t
      };
    case 220:
      return {
        extrusionY: t
      };
    case 230:
      return {
        extrusionZ: t
      };
    case 410:
      return {
        layout: t
      };
    default:
      return {};
  }
}, Ec = "POINT", kd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.x = i;
        break;
      case 20:
        t.y = i;
        break;
      case 30:
        t.z = i;
        break;
      case 39:
        t.thickness = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Ec
  }
), Td = { TYPE: Ec, process: kd }, Bc = "LINE", Xd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.start.x = i;
        break;
      case 20:
        t.start.y = i;
        break;
      case 30:
        t.start.z = i;
        break;
      case 39:
        t.thickness = i;
        break;
      case 11:
        t.end.x = i;
        break;
      case 21:
        t.end.y = i;
        break;
      case 31:
        t.end.z = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Bc,
    start: {},
    end: {}
  }
), zd = { TYPE: Bc, process: Xd }, Uc = "LWPOLYLINE", Wd = (a) => {
  let t;
  return a.reduce(
    (e, s) => {
      const i = s[0], n = s[1];
      switch (i) {
        case 70:
          e.closed = (n & 1) === 1;
          break;
        case 10:
          t = {
            x: n,
            y: 0
          }, e.vertices.push(t);
          break;
        case 20:
          t.y = n;
          break;
        case 39:
          e.thickness = n;
          break;
        case 42:
          t.bulge = n;
          break;
        default:
          Object.assign(e, D(i, n));
          break;
      }
      return e;
    },
    {
      type: Uc,
      vertices: []
    }
  );
}, Id = { TYPE: Uc, process: Wd }, Nc = "POLYLINE", Cd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 70:
        t.closed = (i & 1) === 1, t.polygonMesh = (i & 16) === 16, t.polyfaceMesh = (i & 64) === 64;
        break;
      case 39:
        t.thickness = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Nc,
    vertices: []
  }
), Ed = { TYPE: Nc, process: Cd }, Bd = "VERTEX", Ms = (a) => {
  a.faces = a.faces || [], "x" in a && !a.x && delete a.x, "y" in a && !a.y && delete a.y, "z" in a && !a.z && delete a.z;
}, Ud = (a) => a.reduce((t, e) => {
  const s = e[0], i = e[1];
  switch (s) {
    case 10:
      t.x = i;
      break;
    case 20:
      t.y = i;
      break;
    case 30:
      t.z = i;
      break;
    case 42:
      t.bulge = i;
      break;
    case 71:
      Ms(t), t.faces[0] = i;
      break;
    case 72:
      Ms(t), t.faces[1] = i;
      break;
    case 73:
      Ms(t), t.faces[2] = i;
      break;
    case 74:
      Ms(t), t.faces[3] = i;
      break;
  }
  return t;
}, {}), Nd = { TYPE: Bd, process: Ud }, _c = "CIRCLE", _d = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.x = i;
        break;
      case 20:
        t.y = i;
        break;
      case 30:
        t.z = i;
        break;
      case 40:
        t.r = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: _c
  }
), Hd = { TYPE: _c, process: _d }, Hc = "ARC", Yd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.x = i;
        break;
      case 20:
        t.y = i;
        break;
      case 30:
        t.z = i;
        break;
      case 39:
        t.thickness = i;
        break;
      case 40:
        t.r = i;
        break;
      case 50:
        t.startAngle = i / 180 * Math.PI;
        break;
      case 51:
        t.endAngle = i / 180 * Math.PI;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Hc
  }
), Pd = { TYPE: Hc, process: Yd }, Yc = "ELLIPSE", Ad = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.x = i;
        break;
      case 11:
        t.majorX = i;
        break;
      case 20:
        t.y = i;
        break;
      case 21:
        t.majorY = i;
        break;
      case 30:
        t.z = i;
        break;
      case 31:
        t.majorZ = i;
        break;
      case 40:
        t.axisRatio = i;
        break;
      case 41:
        t.startAngle = i;
        break;
      case 42:
        t.endAngle = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Yc
  }
), Kd = { TYPE: Yc, process: Ad }, Pc = "SPLINE", Jd = (a) => {
  let t;
  return a.reduce(
    (e, s) => {
      const i = s[0], n = s[1];
      switch (i) {
        case 10:
          t = {
            x: n,
            y: 0
          }, e.controlPoints.push(t);
          break;
        case 20:
          t.y = n;
          break;
        case 30:
          t.z = n;
          break;
        case 40:
          e.knots.push(n);
          break;
        case 41:
          e.weights || (e.weights = []), e.weights.push(n);
          break;
        case 42:
          e.knotTolerance = n;
          break;
        case 43:
          e.controlPointTolerance = n;
          break;
        case 44:
          e.fitTolerance = n;
          break;
        case 70:
          e.flag = n, e.closed = (n & 1) === 1;
          break;
        case 71:
          e.degree = n;
          break;
        case 72:
          e.numberOfKnots = n;
          break;
        case 73:
          e.numberOfControlPoints = n;
          break;
        case 74:
          e.numberOfFitPoints = n;
          break;
        default:
          Object.assign(e, D(i, n));
          break;
      }
      return e;
    },
    {
      type: Pc,
      controlPoints: [],
      knots: []
    }
  );
}, Dd = { TYPE: Pc, process: Jd }, Ac = "SOLID", Qd = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.corners[0].x = i;
        break;
      case 20:
        t.corners[0].y = i;
        break;
      case 30:
        t.corners[0].z = i;
        break;
      case 11:
        t.corners[1].x = i;
        break;
      case 21:
        t.corners[1].y = i;
        break;
      case 31:
        t.corners[1].z = i;
        break;
      case 12:
        t.corners[2].x = i;
        break;
      case 22:
        t.corners[2].y = i;
        break;
      case 32:
        t.corners[2].z = i;
        break;
      case 13:
        t.corners[3].x = i;
        break;
      case 23:
        t.corners[3].y = i;
        break;
      case 33:
        t.corners[3].z = i;
        break;
      case 39:
        t.thickness = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Ac,
    corners: [{}, {}, {}, {}]
  }
), jd = { TYPE: Ac, process: Qd }, Kc = "HATCH";
let tt = "IDLE", z = {}, Tt = 0, Kt = !1, ge = null, dt = { references: [], entities: [] }, Ls = null;
const Od = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 100:
        tt = "IDLE";
        break;
      case 2:
        t.patternName = i;
        break;
      case 10:
        tt === "IDLE" ? t.elevation.x = parseFloat(i) : tt === "POLYLINE" ? (Ls = {
          x: parseFloat(i),
          y: 0,
          bulge: 0
        }, dt.entities[0].points.push(Ls)) : tt === "SEED" ? (ge || (ge = { x: 0, y: 0 }, t.seeds.seeds.push(ge)), ge.x = parseFloat(i)) : Xe(s, Tt, parseFloat(i));
        break;
      case 20:
        tt === "IDLE" ? t.elevation.y = parseFloat(i) : tt === "POLYLINE" ? Ls.y = parseFloat(i) : tt === "SEED" ? (ge.y = parseFloat(i), ge = null) : Xe(s, Tt, parseFloat(i));
        break;
      case 30:
        t.elevation.z = parseFloat(i);
        break;
      case 63:
        t.fillColor = i;
        break;
      case 70:
        t.fillType = parseFloat(i) === 1 ? "SOLID" : "PATTERN";
        break;
      case 210:
        t.extrusionDir.x = parseFloat(i);
        break;
      case 220:
        t.extrusionDir.y = parseFloat(i);
        break;
      case 230:
        t.extrusionDir.z = parseFloat(i);
        break;
      case 91:
        t.boundary.count = parseFloat(i);
        break;
      case 92:
        if (dt = { references: [], entities: [] }, t.boundary.loops.push(dt), dt.type = parseFloat(i), Kt = (dt.type & 2) === 2, Kt) {
          const n = {
            type: "POLYLINE",
            points: []
          };
          dt.entities.push(n), tt = "POLYLINE";
        }
        break;
      case 93:
        tt === "IDLE" && (tt = "ENT"), dt.count = parseFloat(i);
        break;
      case 11:
      case 21:
      case 40:
      case 50:
      case 51:
      case 74:
      case 94:
      case 95:
      case 96:
        Tt === 4 && (tt = "SPLINE"), Xe(s, Tt, parseFloat(i));
        break;
      case 42:
        Kt ? Ls.bulge = parseFloat(i) : Xe(s, Tt, parseFloat(i));
        break;
      case 72:
        Tt = parseFloat(i), dt[Kt ? "hasBulge" : "edgeType"] = Tt, Kt || (z = $d(Tt), dt.entities.push(z));
        break;
      case 73:
        tt === "IDLE" || Kt ? dt.entities[0].closed = i : Xe(s, Tt, parseFloat(i));
        break;
      case 75:
        tt = "IDLE", t.style = parseFloat(i);
        break;
      case 76:
        t.hatchType = parseFloat(i);
        break;
      case 97:
        tt = "IDLE", Kt = !1, dt.sourceObjects = parseFloat(i);
        break;
      case 98:
        tt = "SEED", t.seeds.count = parseFloat(i);
        break;
      case 52:
        t.shadowPatternAngle = parseFloat(i);
        break;
      case 41:
        t.spacing = parseFloat(i);
        break;
      case 77:
        t.double = parseFloat(i) === 1;
        break;
      case 78:
        t.pattern.lineCount = parseFloat(i);
        break;
      case 53:
        t.pattern.angle = parseFloat(i);
        break;
      case 43:
        t.pattern.x = parseFloat(i);
        break;
      case 44:
        t.pattern.y = parseFloat(i);
        break;
      case 45:
        t.pattern.offsetX = parseFloat(i);
        break;
      case 46:
        t.pattern.offsetY = parseFloat(i);
        break;
      case 79:
        t.pattern.dashCount = parseFloat(i);
        break;
      case 49:
        t.pattern.length.push(i);
        break;
      case 330:
        dt.references.push(i);
        break;
      case 450:
        t.solidOrGradient = parseFloat(i) === 0 ? "SOLID" : "GRADIENT";
        break;
      case 453:
        t.color.count = parseFloat(i);
        break;
      case 460:
        t.color.rotation = i;
        break;
      case 461:
        t.color.gradient = i;
        break;
      case 462:
        t.color.tint = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Kc,
    elevation: {},
    extrusionDir: { x: 0, y: 0, z: 1 },
    pattern: { length: [] },
    boundary: { loops: [] },
    seeds: { count: 0, seeds: [] },
    color: {}
  }
), qd = { TYPE: Kc, process: Od };
function $d(a) {
  if (Kt) return {};
  switch (a) {
    case 1:
      return {
        type: "LINE",
        start: { x: 0, y: 0 },
        end: { x: 0, y: 0 }
      };
    case 2:
      return {
        type: "ARC",
        center: { x: 0, y: 0 },
        radius: 0,
        startAngle: 0,
        endAngle: 0,
        counterClockWise: !1
      };
    case 3:
      return {
        type: "ELLIPSE",
        center: { x: 0, y: 0 },
        startAngle: 0,
        endAngle: 0,
        counterClockWise: !1,
        major: { x: 0, y: 0 },
        minor: 0
      };
    case 4:
      return {
        type: "SPLINE",
        degree: 0,
        rational: 0,
        periodic: 0,
        knots: { count: 0, knots: [] },
        controlPoints: { count: 0, points: [] },
        weights: 1
      };
  }
  return {};
}
function Xe(a, t, e) {
  switch (a) {
    case 10:
      switch (t) {
        case 1:
          z.start.x = e;
          break;
        case 2:
          z.center.x = e;
          break;
        case 3:
          z.center.x = e;
          break;
        case 4:
          z.controlPoints.points.push({ x: e, y: 0 });
          break;
      }
      break;
    case 20: {
      switch (t) {
        case 1:
          z.start.y = e;
          break;
        case 2:
          z.center.y = e;
          break;
        case 3:
          z.center.y = e;
          break;
        case 4:
          z.controlPoints.points[z.controlPoints.points.length - 1].y = e;
          break;
      }
      break;
    }
    case 11:
      switch (t) {
        case 1:
          z.end.x = e;
          break;
        case 3:
          z.major.x = e;
          break;
      }
      break;
    case 21: {
      switch (t) {
        case 1:
          z.end.y = e;
          break;
        case 3:
          z.major.y = e;
          break;
      }
      break;
    }
    case 40:
      switch (t) {
        case 2:
          z.radius = e;
          break;
        case 3:
          z.minor = e;
          break;
        case 4:
          z.knots.knots.push(e);
          break;
      }
      break;
    case 42:
      switch (t) {
        case 4:
          z.weights = e;
          break;
      }
      break;
    case 50:
      switch (t) {
        case 2:
          z.startAngle = e;
          break;
        case 3:
          z.startAngle = e;
          break;
      }
      break;
    case 51:
      switch (t) {
        case 2:
          z.endAngle = e;
          break;
        case 3:
          z.endAngle = e;
          break;
      }
      break;
    case 73:
      switch (t) {
        case 2:
          z.counterClockWise = parseFloat(e) === 1;
          break;
        case 3:
          z.counterClockWise = parseFloat(e) === 1;
          break;
        case 4:
          z.rational = e;
          break;
      }
      break;
    case 74:
      switch (t) {
        case 4:
          z.periodic = e;
          break;
      }
      break;
    case 94:
      switch (t) {
        case 4:
          z.degree = e;
          break;
      }
      break;
    case 95:
      switch (t) {
        case 4:
          z.knots.count = e;
          break;
      }
      break;
    case 96:
      switch (t) {
        case 4:
          z.controlPoints.count = e;
          break;
      }
      break;
  }
}
const Jc = "MTEXT", uo = {
  10: "x",
  20: "y",
  30: "z",
  40: "nominalTextHeight",
  41: "refRectangleWidth",
  71: "attachmentPoint",
  72: "drawingDirection",
  7: "styleName",
  11: "xAxisX",
  21: "xAxisY",
  31: "xAxisZ",
  42: "horizontalWidth",
  43: "verticalHeight",
  73: "lineSpacingStyle",
  44: "lineSpacingFactor",
  90: "backgroundFill",
  420: "bgColorRGB0",
  421: "bgColorRGB1",
  422: "bgColorRGB2",
  423: "bgColorRGB3",
  424: "bgColorRGB4",
  425: "bgColorRGB5",
  426: "bgColorRGB6",
  427: "bgColorRGB7",
  428: "bgColorRGB8",
  429: "bgColorRGB9",
  430: "bgColorName0",
  431: "bgColorName1",
  432: "bgColorName2",
  433: "bgColorName3",
  434: "bgColorName4",
  435: "bgColorName5",
  436: "bgColorName6",
  437: "bgColorName7",
  438: "bgColorName8",
  439: "bgColorName9",
  45: "fillBoxStyle",
  63: "bgFillColor",
  441: "bgFillTransparency",
  75: "columnType",
  76: "columnCount",
  78: "columnFlowReversed",
  79: "columnAutoheight",
  48: "columnWidth",
  49: "columnGutter",
  50: "columnHeights"
}, t0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    return B(t, s, i), t;
  },
  {
    type: Jc,
    string: ""
  }
), B = (a, t, e) => (uo[t] !== void 0 ? a[uo[t]] = e : t === 1 || t === 3 ? a.string += e : t === 50 ? (a.xAxisX = Math.cos(e), a.xAxisY = Math.sin(e)) : Object.assign(a, D(t, e)), a), e0 = { TYPE: Jc, process: t0, assign: B }, Dc = "TEXT", po = {
  1: "string",
  10: "x",
  20: "y",
  30: "z",
  11: "x2",
  21: "y2",
  31: "z2",
  39: "thickness",
  40: "textHeight",
  41: "relScaleX",
  50: "rotation",
  51: "obliqueAngle",
  7: "styleName",
  71: "mirror",
  72: "hAlign",
  73: "vAlign"
}, s0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    return et(t, s, i), t;
  },
  {
    type: Dc,
    string: ""
  }
), et = (a, t, e) => {
  po[t] !== void 0 ? a[po[t]] = e : Object.assign(a, D(t, e));
}, i0 = { TYPE: Dc, process: s0, assign: et }, Qc = "ATTDEF", n0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    return Or(t, s, i), t;
  },
  {
    type: Qc,
    subclassMarker: "AcDbText",
    thickness: 0,
    scaleX: 1,
    mtext: {},
    text: {}
  }
), Or = (a, t, e) => {
  switch (t) {
    case 100: {
      a.subclassMarker = e;
      break;
    }
    case 1:
      switch (a.subclassMarker) {
        case "AcDbText":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 2:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.tag = e;
          break;
        case "AcDbXrecord":
          a.attdefFlag = e;
          break;
      }
      break;
    case 3:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
          a.prompt = e;
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 7:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 10:
      switch (a.subclassMarker) {
        case "AcDbText":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
        case "AcDbXrecord":
          a.x = e;
          break;
      }
      break;
    case 20:
      switch (a.subclassMarker) {
        case "AcDbText":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
        case "AcDbXrecord":
          a.y = e;
          break;
      }
      break;
    case 30:
      switch (a.subclassMarker) {
        case "AcDbText":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
        case "AcDbXrecord":
          a.z = e;
          break;
      }
      break;
    case 11:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.x2 = e;
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 21:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.y2 = e;
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 31:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.z2 = e;
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 39:
      et(a.text, t, e);
      break;
    case 40:
      switch (a.subclassMarker) {
        case "AcDbText":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
        case "AcDbXrecord":
          a.annotationScale = e;
          break;
      }
      break;
    case 41:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 42:
    case 43:
    case 44:
    case 45:
      B(a.mtext, t, e);
      break;
    case 46:
      a.mtext.annotationHeight = e;
      break;
    case 48:
    case 49:
      B(a.mtext, t, e);
      break;
    case 50:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 51:
      et(a.text, t, e);
      break;
    case 63:
      B(a.mtext, t, e);
      break;
    case 70:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.attributeFlags = e;
          break;
        case "AcDbXrecord":
          typeof a.mTextFlag > "u" ? a.mTextFlag = e : typeof a.isReallyLocked > "u" ? a.isReallyLocked = e : a.secondaryAttdefCount = e;
          break;
      }
      break;
    case 71:
    case 72:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          et(a.text, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 73:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.fieldLength = e;
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 74:
      et(a.text, 73, e);
      break;
    case 75:
    case 76:
    case 78:
    case 79:
      B(a.mtext, t, e);
      break;
    case 90:
      B(a.mtext, t, e);
      break;
    case 210:
    case 220:
    case 230:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          et(a.mtext, t, e);
          break;
        case "AcDbMText":
          B(a.mtext, t, e);
          break;
      }
      break;
    case 280:
      switch (a.subclassMarker) {
        case "AcDbAttributeDefinition":
        case "AcDbAttribute":
          a.lock = e;
          break;
        case "AcDbXrecord":
          a.clone = !0;
          break;
      }
      break;
    case 340:
      a.attdefHandle = e;
      break;
    case 420:
    case 421:
    case 422:
    case 423:
    case 424:
    case 425:
    case 426:
    case 427:
    case 428:
    case 429:
    case 430:
    case 431:
    case 432:
    case 433:
    case 434:
    case 435:
    case 436:
    case 437:
    case 438:
    case 439:
    case 441:
      B(a.mtext, t, e);
      break;
    default:
      Object.assign(a, D(t, e));
      break;
  }
}, r0 = { TYPE: Qc, process: n0, assign: Or }, jc = "ATTRIB", a0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    return Or(t, s, i), t;
  },
  {
    type: jc,
    subclassMarker: "AcDbText",
    thickness: 0,
    scaleX: 1,
    mtext: {},
    text: {}
  }
), o0 = { TYPE: jc, process: a0 }, Oc = "INSERT", l0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 2:
        t.block = i;
        break;
      case 10:
        t.x = i;
        break;
      case 20:
        t.y = i;
        break;
      case 30:
        t.z = i;
        break;
      case 41:
        t.scaleX = i;
        break;
      case 42:
        t.scaleY = i;
        break;
      case 43:
        t.scaleZ = i;
        break;
      case 44:
        t.columnSpacing = i;
        break;
      case 45:
        t.rowSpacing = i;
        break;
      case 50:
        t.rotation = i;
        break;
      case 70:
        t.columnCount = i;
        break;
      case 71:
        t.rowCount = i;
        break;
      case 210:
        t.extrusionX = i;
        break;
      case 220:
        t.extrusionY = i;
        break;
      case 230:
        t.extrusionZ = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: Oc
  }
), c0 = { TYPE: Oc, process: l0 }, qc = "3DFACE", h0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 10:
        t.vertices[0].x = i;
        break;
      case 20:
        t.vertices[0].y = i;
        break;
      case 30:
        t.vertices[0].z = i;
        break;
      case 11:
        t.vertices[1].x = i;
        break;
      case 21:
        t.vertices[1].y = i;
        break;
      case 31:
        t.vertices[1].z = i;
        break;
      case 12:
        t.vertices[2].x = i;
        break;
      case 22:
        t.vertices[2].y = i;
        break;
      case 32:
        t.vertices[2].z = i;
        break;
      case 13:
        t.vertices[3].x = i;
        break;
      case 23:
        t.vertices[3].y = i;
        break;
      case 33:
        t.vertices[3].z = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: qc,
    vertices: [{}, {}, {}, {}]
  }
), u0 = { TYPE: qc, process: h0 }, $c = "DIMENSION", d0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 2:
        t.block = i;
        break;
      case 10:
        t.start.x = i;
        break;
      case 20:
        t.start.y = i;
        break;
      case 30:
        t.start.z = i;
        break;
      case 11:
        t.textMidpoint.x = i;
        break;
      case 21:
        t.textMidpoint.y = i;
        break;
      case 31:
        t.textMidpoint.z = i;
        break;
      case 13:
        t.measureStart.x = i;
        break;
      case 23:
        t.measureStart.y = i;
        break;
      case 33:
        t.measureStart.z = i;
        break;
      case 14:
        t.measureEnd.x = i;
        break;
      case 24:
        t.measureEnd.y = i;
        break;
      case 34:
        t.measureEnd.z = i;
        break;
      case 50:
        t.rotation = i;
        break;
      case 51:
        t.horizonRotation = i;
        break;
      case 52:
        t.extensionRotation = i;
        break;
      case 53:
        t.textRotation = i;
        break;
      case 70: {
        const n = p0(i);
        n.ordinateType && (t.ordinateType = !0), n.uniqueBlockReference && (t.uniqueBlockReference = !0), n.userDefinedLocation && (t.userDefinedLocation = !0), t.dimensionType = n.dimensionType;
        break;
      }
      case 71:
        t.attachementPoint = i;
        break;
      case 210:
        t.extrudeDirection = t.extrudeDirection || {}, t.extrudeDirection.x = i;
        break;
      case 220:
        t.extrudeDirection = t.extrudeDirection || {}, t.extrudeDirection.y = i;
        break;
      case 230:
        t.extrudeDirection = t.extrudeDirection || {}, t.extrudeDirection.z = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: $c,
    start: { x: 0, y: 0, z: 0 },
    measureStart: { x: 0, y: 0, z: 0 },
    measureEnd: { x: 0, y: 0, z: 0 },
    textMidpoint: { x: 0, y: 0, z: 0 },
    attachementPoint: 1,
    dimensionType: 0
  }
);
function p0(a) {
  let t = !1, e = !1, s = !1;
  if (a > 6) {
    const i = a - 32, n = a - 64, r = a - 32 - 64, o = a - 32 - 128, l = a - 32 - 64 - 128;
    i >= 0 && i <= 6 ? (t = !0, a = i) : n >= 0 && n <= 6 ? (e = !0, a = n) : r >= 0 && r <= 6 ? (t = !0, e = !0, a = r) : o >= 0 && o <= 6 ? (t = !0, s = !0, a = o) : l >= 0 && l <= 6 && (t = !0, e = !0, s = !0, a = l);
  }
  return {
    dimensionType: a,
    uniqueBlockReference: t,
    ordinateType: e,
    userDefinedLocation: s
  };
}
const m0 = { TYPE: $c, process: d0 }, th = "VIEWPORT", f0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 1:
        t.layout = parseFloat(i);
        break;
      case 10:
        t.center.x = parseFloat(i);
        break;
      case 20:
        t.center.y = parseFloat(i);
        break;
      case 30:
        t.center.z = parseFloat(i);
        break;
      case 12:
        t.centerDCS.x = parseFloat(i);
        break;
      case 22:
        t.centerDCS.y = parseFloat(i);
        break;
      case 13:
        t.snap.x = parseFloat(i);
        break;
      case 23:
        t.snap.y = parseFloat(i);
        break;
      case 14:
        t.snapSpacing.x = parseFloat(i);
        break;
      case 24:
        t.snapSpacing.y = parseFloat(i);
        break;
      case 15:
        t.gridSpacing.x = parseFloat(i);
        break;
      case 25:
        t.gridSpacing.y = parseFloat(i);
        break;
      case 16:
        t.direction.x = parseFloat(i);
        break;
      case 26:
        t.direction.y = parseFloat(i);
        break;
      case 36:
        t.direction.z = parseFloat(i);
        break;
      case 17:
        t.target.x = parseFloat(i);
        break;
      case 27:
        t.target.y = parseFloat(i);
        break;
      case 37:
        t.target.z = parseFloat(i);
        break;
      case 40:
        t.width = parseFloat(i);
        break;
      case 41:
        t.height = parseFloat(i);
        break;
      case 50:
        t.snapAngle = parseFloat(i);
        break;
      case 51:
        t.angle = parseFloat(i);
        break;
      case 68:
        t.status = i;
        break;
      case 69:
        t.id = i;
        break;
      case 90:
        t.flags = i;
        break;
      case 110:
        t.x = parseFloat(i);
        break;
      case 120:
        t.y = parseFloat(i);
        break;
      case 130:
        t.z = parseFloat(i);
        break;
      case 111:
        t.xAxisX = parseFloat(i);
        break;
      case 121:
        t.xAxisY = parseFloat(i);
        break;
      case 131:
        t.xAxisZ = parseFloat(i);
        break;
      case 112:
        t.xAxisX = parseFloat(i);
        break;
      case 122:
        t.xAxisY = parseFloat(i);
        break;
      case 132:
        t.xAxisZ = parseFloat(i);
        break;
      case 146:
        t.elevation = parseFloat(i);
        break;
      case 281:
        t.render = i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: th,
    center: {},
    centerDCS: {},
    snap: {},
    snapSpacing: {},
    gridSpacing: {},
    direction: {},
    target: {}
  }
), b0 = { TYPE: th, process: f0 }, eh = "OLE2FRAME", y0 = (a) => a.reduce(
  (t, e) => {
    const s = e[0], i = e[1];
    switch (s) {
      case 70:
        t.version = i;
        break;
      case 3:
        t.name = i;
        break;
      case 10:
        t.upperLeftX = i;
        break;
      case 20:
        t.upperLeftY = i;
        break;
      case 30:
        t.upperLeftZ = i;
        break;
      case 11:
        t.lowerRightX = i;
        break;
      case 21:
        t.lowerRightY = i;
        break;
      case 31:
        t.lowerRightZ = i;
        break;
      case 71:
        t.objectType = i;
        break;
      case 72:
        t.tile = i;
        break;
      case 90:
        t.length = i;
        break;
      case 310:
        t.data += i;
        break;
      default:
        Object.assign(t, D(s, i));
        break;
    }
    return t;
  },
  {
    type: eh,
    data: ""
  }
), x0 = { TYPE: eh, process: y0 }, mo = [
  Td,
  zd,
  Id,
  Ed,
  Nd,
  Hd,
  Pd,
  Kd,
  Dd,
  jd,
  qd,
  e0,
  r0,
  o0,
  i0,
  c0,
  m0,
  u0,
  b0,
  x0
].reduce((a, t) => (a[t.TYPE] = t, a), {}), sh = (a) => {
  const t = [], e = [];
  let s;
  a.forEach((n) => {
    n[0] === 0 && (s = [], e.push(s)), s.push(n);
  });
  let i;
  return e.forEach((n) => {
    const r = n[0][1], o = n.slice(1);
    if (mo[r] !== void 0) {
      const l = mo[r].process(o);
      r === "POLYLINE" ? (i = l, t.push(l)) : r === "VERTEX" ? i ? i.vertices.push(l) : ie.error("ignoring invalid VERTEX entity") : r === "SEQEND" ? i = void 0 : t.push(l);
    }
  }), t;
}, g0 = (a) => {
  let t;
  const e = [];
  let s, i = [];
  return a.forEach((n) => {
    const r = n[0], o = n[1];
    if (o === "BLOCK")
      t = "block", s = {}, i = [], e.push(s);
    else if (o === "ENDBLK")
      t === "entities" ? s.entities = sh(i) : s.entities = [], i = void 0, t = void 0;
    else if (t === "block" && r !== 0)
      switch (r) {
        case 1:
          s.xref = o;
          break;
        case 2:
          s.name = o;
          break;
        case 10:
          s.x = o;
          break;
        case 20:
          s.y = o;
          break;
        case 30:
          s.z = o;
          break;
        case 67:
          o !== 0 && (s.paperSpace = o);
          break;
        case 410:
          s.layout = o;
          break;
      }
    else t === "block" && r === 0 ? (t = "entities", i.push(n)) : t === "entities" && i.push(n);
  }), e;
}, R0 = (a) => {
  let t;
  const e = {
    layouts: []
  };
  let s = {};
  return a.forEach((i, n) => {
    const r = i[0], o = i[1];
    if (r === 0 && (t = "IDLE"), o === "LAYOUT" && (t = "layout", s = {}, e.layouts.push(s)), t === "layout" && r !== 0)
      switch (r) {
        case 100:
          o === "AcDbLayout" && (t = "AcDbLayout");
          break;
      }
    if (t === "AcDbLayout" && r !== 0)
      switch (r) {
        case 1:
          s.name = o;
          break;
        case 5:
          s.handle = o;
          break;
        case 10:
          s.minLimitX = parseFloat(o);
          break;
        case 20:
          s.minLimitY = parseFloat(o);
          break;
        case 11:
          s.maxLimitX = parseFloat(o);
          break;
        case 21:
          s.maxLimitY = parseFloat(o);
          break;
        case 12:
          s.x = parseFloat(o);
          break;
        case 22:
          s.y = parseFloat(o);
          break;
        case 32:
          s.z = parseFloat(o);
          break;
        case 14:
          s.minX = parseFloat(o);
          break;
        case 24:
          s.minY = parseFloat(o);
          break;
        case 34:
          s.minZ = parseFloat(o);
          break;
        case 15:
          s.maxX = parseFloat(o);
          break;
        case 25:
          s.maxY = parseFloat(o);
          break;
        case 35:
          s.maxZ = parseFloat(o);
          break;
        case 70:
          s.flag = o === 1 ? "PSLTSCALE" : "LIMCHECK";
          break;
        case 71:
          s.tabOrder = o;
          break;
        case 146:
          s.elevation = parseFloat(o);
          break;
        case 13:
          s.ucsX = parseFloat(o);
          break;
        case 23:
          s.ucsY = parseFloat(o);
          break;
        case 33:
          s.ucsZ = parseFloat(o);
          break;
        case 16:
          s.ucsXaxisX = parseFloat(o);
          break;
        case 26:
          s.ucsXaxisY = parseFloat(o);
          break;
        case 36:
          s.ucsXaxisZ = parseFloat(o);
          break;
        case 17:
          s.ucsYaxisX = parseFloat(o);
          break;
        case 27:
          s.ucsYaxisY = parseFloat(o);
          break;
        case 37:
          s.ucsYaxisZ = parseFloat(o);
          break;
        case 76:
          switch (o) {
            case 0:
              s.ucsType = "NOT ORTHOGRAPHIC";
              break;
            case 1:
              s.ucsType = "TOP";
              break;
            case 2:
              s.ucsType = "BOTTOM";
              break;
            case 3:
              s.ucsType = "FRONT";
              break;
            case 4:
              s.ucsType = "BACK";
              break;
            case 5:
              s.ucsType = "LEFT";
              break;
            case 6:
              s.ucsType = "RIGHT";
              break;
          }
          break;
        case 330:
          s.tableRecord = o;
          break;
        case 331:
          s.lastActiveViewport = o;
          break;
        case 333:
          s.shadePlot = o;
          break;
      }
  }), e;
}, S0 = (a, t) => a >= 10 && a < 60 || a >= 210 && a < 240 ? parseFloat(t, 10) : a >= 60 && a < 100 ? parseInt(t, 10) : t, Z0 = (a) => {
  let t = "type", e;
  const s = [];
  for (const i of a)
    t === "type" ? (e = parseInt(i, 10), t = "value") : (s.push([e, S0(e, i)]), t = "type");
  return s;
}, G0 = (a) => {
  let t;
  return a.reduce((e, s) => (s[0] === 0 && s[1] === "SECTION" ? t = [] : s[0] === 0 && s[1] === "ENDSEC" ? (e.push(t), t = void 0) : t !== void 0 && t.push(s), e), []);
}, V0 = (a, t) => {
  const e = t[0][1], s = t.slice(1);
  switch (e) {
    case "HEADER":
      a.header = Vd(s);
      break;
    case "TABLES":
      a.tables = Fd(s);
      break;
    case "BLOCKS":
      a.blocks = g0(s);
      break;
    case "ENTITIES":
      a.entities = sh(s);
      break;
    case "OBJECTS":
      a.objects = R0(s);
      break;
  }
  return a;
}, M0 = (a) => {
  const t = a.split(/\r\n|\r|\n/g), e = Z0(t);
  return G0(e).reduce(V0, {
    // Start with empty defaults in the event of empty sections
    header: {},
    blocks: [],
    entities: [],
    objects: { layouts: [] },
    tables: { layers: {}, styles: {}, ltypes: {} }
  });
};
var Xi, fo;
function L0() {
  if (fo) return Xi;
  fo = 1;
  function a() {
    this.__data__ = [], this.size = 0;
  }
  return Xi = a, Xi;
}
var zi, bo;
function ih() {
  if (bo) return zi;
  bo = 1;
  function a(t, e) {
    return t === e || t !== t && e !== e;
  }
  return zi = a, zi;
}
var Wi, yo;
function Cs() {
  if (yo) return Wi;
  yo = 1;
  var a = ih();
  function t(e, s) {
    for (var i = e.length; i--; )
      if (a(e[i][0], s))
        return i;
    return -1;
  }
  return Wi = t, Wi;
}
var Ii, xo;
function w0() {
  if (xo) return Ii;
  xo = 1;
  var a = Cs(), t = Array.prototype, e = t.splice;
  function s(i) {
    var n = this.__data__, r = a(n, i);
    if (r < 0)
      return !1;
    var o = n.length - 1;
    return r == o ? n.pop() : e.call(n, r, 1), --this.size, !0;
  }
  return Ii = s, Ii;
}
var Ci, go;
function v0() {
  if (go) return Ci;
  go = 1;
  var a = Cs();
  function t(e) {
    var s = this.__data__, i = a(s, e);
    return i < 0 ? void 0 : s[i][1];
  }
  return Ci = t, Ci;
}
var Ei, Ro;
function F0() {
  if (Ro) return Ei;
  Ro = 1;
  var a = Cs();
  function t(e) {
    return a(this.__data__, e) > -1;
  }
  return Ei = t, Ei;
}
var Bi, So;
function k0() {
  if (So) return Bi;
  So = 1;
  var a = Cs();
  function t(e, s) {
    var i = this.__data__, n = a(i, e);
    return n < 0 ? (++this.size, i.push([e, s])) : i[n][1] = s, this;
  }
  return Bi = t, Bi;
}
var Ui, Zo;
function Es() {
  if (Zo) return Ui;
  Zo = 1;
  var a = L0(), t = w0(), e = v0(), s = F0(), i = k0();
  function n(r) {
    var o = -1, l = r == null ? 0 : r.length;
    for (this.clear(); ++o < l; ) {
      var c = r[o];
      this.set(c[0], c[1]);
    }
  }
  return n.prototype.clear = a, n.prototype.delete = t, n.prototype.get = e, n.prototype.has = s, n.prototype.set = i, Ui = n, Ui;
}
var Ni, Go;
function T0() {
  if (Go) return Ni;
  Go = 1;
  var a = Es();
  function t() {
    this.__data__ = new a(), this.size = 0;
  }
  return Ni = t, Ni;
}
var _i, Vo;
function X0() {
  if (Vo) return _i;
  Vo = 1;
  function a(t) {
    var e = this.__data__, s = e.delete(t);
    return this.size = e.size, s;
  }
  return _i = a, _i;
}
var Hi, Mo;
function z0() {
  if (Mo) return Hi;
  Mo = 1;
  function a(t) {
    return this.__data__.get(t);
  }
  return Hi = a, Hi;
}
var Yi, Lo;
function W0() {
  if (Lo) return Yi;
  Lo = 1;
  function a(t) {
    return this.__data__.has(t);
  }
  return Yi = a, Yi;
}
var Pi, wo;
function nh() {
  if (wo) return Pi;
  wo = 1;
  var a = typeof Zs == "object" && Zs && Zs.Object === Object && Zs;
  return Pi = a, Pi;
}
var Ai, vo;
function Vt() {
  if (vo) return Ai;
  vo = 1;
  var a = nh(), t = typeof self == "object" && self && self.Object === Object && self, e = a || t || Function("return this")();
  return Ai = e, Ai;
}
var Ki, Fo;
function qr() {
  if (Fo) return Ki;
  Fo = 1;
  var a = Vt(), t = a.Symbol;
  return Ki = t, Ki;
}
var Ji, ko;
function I0() {
  if (ko) return Ji;
  ko = 1;
  var a = qr(), t = Object.prototype, e = t.hasOwnProperty, s = t.toString, i = a ? a.toStringTag : void 0;
  function n(r) {
    var o = e.call(r, i), l = r[i];
    try {
      r[i] = void 0;
      var c = !0;
    } catch {
    }
    var h = s.call(r);
    return c && (o ? r[i] = l : delete r[i]), h;
  }
  return Ji = n, Ji;
}
var Di, To;
function C0() {
  if (To) return Di;
  To = 1;
  var a = Object.prototype, t = a.toString;
  function e(s) {
    return t.call(s);
  }
  return Di = e, Di;
}
var Qi, Xo;
function Bs() {
  if (Xo) return Qi;
  Xo = 1;
  var a = qr(), t = I0(), e = C0(), s = "[object Null]", i = "[object Undefined]", n = a ? a.toStringTag : void 0;
  function r(o) {
    return o == null ? o === void 0 ? i : s : n && n in Object(o) ? t(o) : e(o);
  }
  return Qi = r, Qi;
}
var ji, zo;
function Je() {
  if (zo) return ji;
  zo = 1;
  function a(t) {
    var e = typeof t;
    return t != null && (e == "object" || e == "function");
  }
  return ji = a, ji;
}
var Oi, Wo;
function rh() {
  if (Wo) return Oi;
  Wo = 1;
  var a = Bs(), t = Je(), e = "[object AsyncFunction]", s = "[object Function]", i = "[object GeneratorFunction]", n = "[object Proxy]";
  function r(o) {
    if (!t(o))
      return !1;
    var l = a(o);
    return l == s || l == i || l == e || l == n;
  }
  return Oi = r, Oi;
}
var qi, Io;
function E0() {
  if (Io) return qi;
  Io = 1;
  var a = Vt(), t = a["__core-js_shared__"];
  return qi = t, qi;
}
var $i, Co;
function B0() {
  if (Co) return $i;
  Co = 1;
  var a = E0(), t = function() {
    var s = /[^.]+$/.exec(a && a.keys && a.keys.IE_PROTO || "");
    return s ? "Symbol(src)_1." + s : "";
  }();
  function e(s) {
    return !!t && t in s;
  }
  return $i = e, $i;
}
var tn, Eo;
function ah() {
  if (Eo) return tn;
  Eo = 1;
  var a = Function.prototype, t = a.toString;
  function e(s) {
    if (s != null) {
      try {
        return t.call(s);
      } catch {
      }
      try {
        return s + "";
      } catch {
      }
    }
    return "";
  }
  return tn = e, tn;
}
var en, Bo;
function U0() {
  if (Bo) return en;
  Bo = 1;
  var a = rh(), t = B0(), e = Je(), s = ah(), i = /[\\^$.*+?()[\]{}|]/g, n = /^\[object .+?Constructor\]$/, r = Function.prototype, o = Object.prototype, l = r.toString, c = o.hasOwnProperty, h = RegExp(
    "^" + l.call(c).replace(i, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$"
  );
  function u(d) {
    if (!e(d) || t(d))
      return !1;
    var p = a(d) ? h : n;
    return p.test(s(d));
  }
  return en = u, en;
}
var sn, Uo;
function N0() {
  if (Uo) return sn;
  Uo = 1;
  function a(t, e) {
    return t == null ? void 0 : t[e];
  }
  return sn = a, sn;
}
var nn, No;
function ne() {
  if (No) return nn;
  No = 1;
  var a = U0(), t = N0();
  function e(s, i) {
    var n = t(s, i);
    return a(n) ? n : void 0;
  }
  return nn = e, nn;
}
var rn, _o;
function $r() {
  if (_o) return rn;
  _o = 1;
  var a = ne(), t = Vt(), e = a(t, "Map");
  return rn = e, rn;
}
var an, Ho;
function Us() {
  if (Ho) return an;
  Ho = 1;
  var a = ne(), t = a(Object, "create");
  return an = t, an;
}
var on, Yo;
function _0() {
  if (Yo) return on;
  Yo = 1;
  var a = Us();
  function t() {
    this.__data__ = a ? a(null) : {}, this.size = 0;
  }
  return on = t, on;
}
var ln, Po;
function H0() {
  if (Po) return ln;
  Po = 1;
  function a(t) {
    var e = this.has(t) && delete this.__data__[t];
    return this.size -= e ? 1 : 0, e;
  }
  return ln = a, ln;
}
var cn, Ao;
function Y0() {
  if (Ao) return cn;
  Ao = 1;
  var a = Us(), t = "__lodash_hash_undefined__", e = Object.prototype, s = e.hasOwnProperty;
  function i(n) {
    var r = this.__data__;
    if (a) {
      var o = r[n];
      return o === t ? void 0 : o;
    }
    return s.call(r, n) ? r[n] : void 0;
  }
  return cn = i, cn;
}
var hn, Ko;
function P0() {
  if (Ko) return hn;
  Ko = 1;
  var a = Us(), t = Object.prototype, e = t.hasOwnProperty;
  function s(i) {
    var n = this.__data__;
    return a ? n[i] !== void 0 : e.call(n, i);
  }
  return hn = s, hn;
}
var un, Jo;
function A0() {
  if (Jo) return un;
  Jo = 1;
  var a = Us(), t = "__lodash_hash_undefined__";
  function e(s, i) {
    var n = this.__data__;
    return this.size += this.has(s) ? 0 : 1, n[s] = a && i === void 0 ? t : i, this;
  }
  return un = e, un;
}
var dn, Do;
function K0() {
  if (Do) return dn;
  Do = 1;
  var a = _0(), t = H0(), e = Y0(), s = P0(), i = A0();
  function n(r) {
    var o = -1, l = r == null ? 0 : r.length;
    for (this.clear(); ++o < l; ) {
      var c = r[o];
      this.set(c[0], c[1]);
    }
  }
  return n.prototype.clear = a, n.prototype.delete = t, n.prototype.get = e, n.prototype.has = s, n.prototype.set = i, dn = n, dn;
}
var pn, Qo;
function J0() {
  if (Qo) return pn;
  Qo = 1;
  var a = K0(), t = Es(), e = $r();
  function s() {
    this.size = 0, this.__data__ = {
      hash: new a(),
      map: new (e || t)(),
      string: new a()
    };
  }
  return pn = s, pn;
}
var mn, jo;
function D0() {
  if (jo) return mn;
  jo = 1;
  function a(t) {
    var e = typeof t;
    return e == "string" || e == "number" || e == "symbol" || e == "boolean" ? t !== "__proto__" : t === null;
  }
  return mn = a, mn;
}
var fn, Oo;
function Ns() {
  if (Oo) return fn;
  Oo = 1;
  var a = D0();
  function t(e, s) {
    var i = e.__data__;
    return a(s) ? i[typeof s == "string" ? "string" : "hash"] : i.map;
  }
  return fn = t, fn;
}
var bn, qo;
function Q0() {
  if (qo) return bn;
  qo = 1;
  var a = Ns();
  function t(e) {
    var s = a(this, e).delete(e);
    return this.size -= s ? 1 : 0, s;
  }
  return bn = t, bn;
}
var yn, $o;
function j0() {
  if ($o) return yn;
  $o = 1;
  var a = Ns();
  function t(e) {
    return a(this, e).get(e);
  }
  return yn = t, yn;
}
var xn, tl;
function O0() {
  if (tl) return xn;
  tl = 1;
  var a = Ns();
  function t(e) {
    return a(this, e).has(e);
  }
  return xn = t, xn;
}
var gn, el;
function q0() {
  if (el) return gn;
  el = 1;
  var a = Ns();
  function t(e, s) {
    var i = a(this, e), n = i.size;
    return i.set(e, s), this.size += i.size == n ? 0 : 1, this;
  }
  return gn = t, gn;
}
var Rn, sl;
function $0() {
  if (sl) return Rn;
  sl = 1;
  var a = J0(), t = Q0(), e = j0(), s = O0(), i = q0();
  function n(r) {
    var o = -1, l = r == null ? 0 : r.length;
    for (this.clear(); ++o < l; ) {
      var c = r[o];
      this.set(c[0], c[1]);
    }
  }
  return n.prototype.clear = a, n.prototype.delete = t, n.prototype.get = e, n.prototype.has = s, n.prototype.set = i, Rn = n, Rn;
}
var Sn, il;
function tp() {
  if (il) return Sn;
  il = 1;
  var a = Es(), t = $r(), e = $0(), s = 200;
  function i(n, r) {
    var o = this.__data__;
    if (o instanceof a) {
      var l = o.__data__;
      if (!t || l.length < s - 1)
        return l.push([n, r]), this.size = ++o.size, this;
      o = this.__data__ = new e(l);
    }
    return o.set(n, r), this.size = o.size, this;
  }
  return Sn = i, Sn;
}
var Zn, nl;
function ep() {
  if (nl) return Zn;
  nl = 1;
  var a = Es(), t = T0(), e = X0(), s = z0(), i = W0(), n = tp();
  function r(o) {
    var l = this.__data__ = new a(o);
    this.size = l.size;
  }
  return r.prototype.clear = t, r.prototype.delete = e, r.prototype.get = s, r.prototype.has = i, r.prototype.set = n, Zn = r, Zn;
}
var Gn, rl;
function sp() {
  if (rl) return Gn;
  rl = 1;
  function a(t, e) {
    for (var s = -1, i = t == null ? 0 : t.length; ++s < i && e(t[s], s, t) !== !1; )
      ;
    return t;
  }
  return Gn = a, Gn;
}
var Vn, al;
function ip() {
  if (al) return Vn;
  al = 1;
  var a = ne(), t = function() {
    try {
      var e = a(Object, "defineProperty");
      return e({}, "", {}), e;
    } catch {
    }
  }();
  return Vn = t, Vn;
}
var Mn, ol;
function oh() {
  if (ol) return Mn;
  ol = 1;
  var a = ip();
  function t(e, s, i) {
    s == "__proto__" && a ? a(e, s, {
      configurable: !0,
      enumerable: !0,
      value: i,
      writable: !0
    }) : e[s] = i;
  }
  return Mn = t, Mn;
}
var Ln, ll;
function lh() {
  if (ll) return Ln;
  ll = 1;
  var a = oh(), t = ih(), e = Object.prototype, s = e.hasOwnProperty;
  function i(n, r, o) {
    var l = n[r];
    (!(s.call(n, r) && t(l, o)) || o === void 0 && !(r in n)) && a(n, r, o);
  }
  return Ln = i, Ln;
}
var wn, cl;
function _s() {
  if (cl) return wn;
  cl = 1;
  var a = lh(), t = oh();
  function e(s, i, n, r) {
    var o = !n;
    n || (n = {});
    for (var l = -1, c = i.length; ++l < c; ) {
      var h = i[l], u = r ? r(n[h], s[h], h, n, s) : void 0;
      u === void 0 && (u = s[h]), o ? t(n, h, u) : a(n, h, u);
    }
    return n;
  }
  return wn = e, wn;
}
var vn, hl;
function np() {
  if (hl) return vn;
  hl = 1;
  function a(t, e) {
    for (var s = -1, i = Array(t); ++s < t; )
      i[s] = e(s);
    return i;
  }
  return vn = a, vn;
}
var Fn, ul;
function De() {
  if (ul) return Fn;
  ul = 1;
  function a(t) {
    return t != null && typeof t == "object";
  }
  return Fn = a, Fn;
}
var kn, dl;
function rp() {
  if (dl) return kn;
  dl = 1;
  var a = Bs(), t = De(), e = "[object Arguments]";
  function s(i) {
    return t(i) && a(i) == e;
  }
  return kn = s, kn;
}
var Tn, pl;
function ap() {
  if (pl) return Tn;
  pl = 1;
  var a = rp(), t = De(), e = Object.prototype, s = e.hasOwnProperty, i = e.propertyIsEnumerable, n = a(/* @__PURE__ */ function() {
    return arguments;
  }()) ? a : function(r) {
    return t(r) && s.call(r, "callee") && !i.call(r, "callee");
  };
  return Tn = n, Tn;
}
var Xn, ml;
function ta() {
  if (ml) return Xn;
  ml = 1;
  var a = Array.isArray;
  return Xn = a, Xn;
}
var We = { exports: {} }, zn, fl;
function op() {
  if (fl) return zn;
  fl = 1;
  function a() {
    return !1;
  }
  return zn = a, zn;
}
We.exports;
var bl;
function ch() {
  return bl || (bl = 1, function(a, t) {
    var e = Vt(), s = op(), i = t && !t.nodeType && t, n = i && !0 && a && !a.nodeType && a, r = n && n.exports === i, o = r ? e.Buffer : void 0, l = o ? o.isBuffer : void 0, c = l || s;
    a.exports = c;
  }(We, We.exports)), We.exports;
}
var Wn, yl;
function lp() {
  if (yl) return Wn;
  yl = 1;
  var a = 9007199254740991, t = /^(?:0|[1-9]\d*)$/;
  function e(s, i) {
    var n = typeof s;
    return i = i ?? a, !!i && (n == "number" || n != "symbol" && t.test(s)) && s > -1 && s % 1 == 0 && s < i;
  }
  return Wn = e, Wn;
}
var In, xl;
function hh() {
  if (xl) return In;
  xl = 1;
  var a = 9007199254740991;
  function t(e) {
    return typeof e == "number" && e > -1 && e % 1 == 0 && e <= a;
  }
  return In = t, In;
}
var Cn, gl;
function cp() {
  if (gl) return Cn;
  gl = 1;
  var a = Bs(), t = hh(), e = De(), s = "[object Arguments]", i = "[object Array]", n = "[object Boolean]", r = "[object Date]", o = "[object Error]", l = "[object Function]", c = "[object Map]", h = "[object Number]", u = "[object Object]", d = "[object RegExp]", p = "[object Set]", m = "[object String]", f = "[object WeakMap]", b = "[object ArrayBuffer]", y = "[object DataView]", x = "[object Float32Array]", g = "[object Float64Array]", S = "[object Int8Array]", Z = "[object Int16Array]", V = "[object Int32Array]", G = "[object Uint8Array]", M = "[object Uint8ClampedArray]", L = "[object Uint16Array]", k = "[object Uint32Array]", w = {};
  w[x] = w[g] = w[S] = w[Z] = w[V] = w[G] = w[M] = w[L] = w[k] = !0, w[s] = w[i] = w[b] = w[n] = w[y] = w[r] = w[o] = w[l] = w[c] = w[h] = w[u] = w[d] = w[p] = w[m] = w[f] = !1;
  function E(F) {
    return e(F) && t(F.length) && !!w[a(F)];
  }
  return Cn = E, Cn;
}
var En, Rl;
function ea() {
  if (Rl) return En;
  Rl = 1;
  function a(t) {
    return function(e) {
      return t(e);
    };
  }
  return En = a, En;
}
var Ie = { exports: {} };
Ie.exports;
var Sl;
function sa() {
  return Sl || (Sl = 1, function(a, t) {
    var e = nh(), s = t && !t.nodeType && t, i = s && !0 && a && !a.nodeType && a, n = i && i.exports === s, r = n && e.process, o = function() {
      try {
        var l = i && i.require && i.require("util").types;
        return l || r && r.binding && r.binding("util");
      } catch {
      }
    }();
    a.exports = o;
  }(Ie, Ie.exports)), Ie.exports;
}
var Bn, Zl;
function hp() {
  if (Zl) return Bn;
  Zl = 1;
  var a = cp(), t = ea(), e = sa(), s = e && e.isTypedArray, i = s ? t(s) : a;
  return Bn = i, Bn;
}
var Un, Gl;
function uh() {
  if (Gl) return Un;
  Gl = 1;
  var a = np(), t = ap(), e = ta(), s = ch(), i = lp(), n = hp(), r = Object.prototype, o = r.hasOwnProperty;
  function l(c, h) {
    var u = e(c), d = !u && t(c), p = !u && !d && s(c), m = !u && !d && !p && n(c), f = u || d || p || m, b = f ? a(c.length, String) : [], y = b.length;
    for (var x in c)
      (h || o.call(c, x)) && !(f && // Safari 9 has enumerable `arguments.length` in strict mode.
      (x == "length" || // Node.js 0.10 has enumerable non-index properties on buffers.
      p && (x == "offset" || x == "parent") || // PhantomJS 2 has enumerable non-index properties on typed arrays.
      m && (x == "buffer" || x == "byteLength" || x == "byteOffset") || // Skip index properties.
      i(x, y))) && b.push(x);
    return b;
  }
  return Un = l, Un;
}
var Nn, Vl;
function ia() {
  if (Vl) return Nn;
  Vl = 1;
  var a = Object.prototype;
  function t(e) {
    var s = e && e.constructor, i = typeof s == "function" && s.prototype || a;
    return e === i;
  }
  return Nn = t, Nn;
}
var _n, Ml;
function dh() {
  if (Ml) return _n;
  Ml = 1;
  function a(t, e) {
    return function(s) {
      return t(e(s));
    };
  }
  return _n = a, _n;
}
var Hn, Ll;
function up() {
  if (Ll) return Hn;
  Ll = 1;
  var a = dh(), t = a(Object.keys, Object);
  return Hn = t, Hn;
}
var Yn, wl;
function dp() {
  if (wl) return Yn;
  wl = 1;
  var a = ia(), t = up(), e = Object.prototype, s = e.hasOwnProperty;
  function i(n) {
    if (!a(n))
      return t(n);
    var r = [];
    for (var o in Object(n))
      s.call(n, o) && o != "constructor" && r.push(o);
    return r;
  }
  return Yn = i, Yn;
}
var Pn, vl;
function ph() {
  if (vl) return Pn;
  vl = 1;
  var a = rh(), t = hh();
  function e(s) {
    return s != null && t(s.length) && !a(s);
  }
  return Pn = e, Pn;
}
var An, Fl;
function na() {
  if (Fl) return An;
  Fl = 1;
  var a = uh(), t = dp(), e = ph();
  function s(i) {
    return e(i) ? a(i) : t(i);
  }
  return An = s, An;
}
var Kn, kl;
function pp() {
  if (kl) return Kn;
  kl = 1;
  var a = _s(), t = na();
  function e(s, i) {
    return s && a(i, t(i), s);
  }
  return Kn = e, Kn;
}
var Jn, Tl;
function mp() {
  if (Tl) return Jn;
  Tl = 1;
  function a(t) {
    var e = [];
    if (t != null)
      for (var s in Object(t))
        e.push(s);
    return e;
  }
  return Jn = a, Jn;
}
var Dn, Xl;
function fp() {
  if (Xl) return Dn;
  Xl = 1;
  var a = Je(), t = ia(), e = mp(), s = Object.prototype, i = s.hasOwnProperty;
  function n(r) {
    if (!a(r))
      return e(r);
    var o = t(r), l = [];
    for (var c in r)
      c == "constructor" && (o || !i.call(r, c)) || l.push(c);
    return l;
  }
  return Dn = n, Dn;
}
var Qn, zl;
function ra() {
  if (zl) return Qn;
  zl = 1;
  var a = uh(), t = fp(), e = ph();
  function s(i) {
    return e(i) ? a(i, !0) : t(i);
  }
  return Qn = s, Qn;
}
var jn, Wl;
function bp() {
  if (Wl) return jn;
  Wl = 1;
  var a = _s(), t = ra();
  function e(s, i) {
    return s && a(i, t(i), s);
  }
  return jn = e, jn;
}
var Ce = { exports: {} };
Ce.exports;
var Il;
function yp() {
  return Il || (Il = 1, function(a, t) {
    var e = Vt(), s = t && !t.nodeType && t, i = s && !0 && a && !a.nodeType && a, n = i && i.exports === s, r = n ? e.Buffer : void 0, o = r ? r.allocUnsafe : void 0;
    function l(c, h) {
      if (h)
        return c.slice();
      var u = c.length, d = o ? o(u) : new c.constructor(u);
      return c.copy(d), d;
    }
    a.exports = l;
  }(Ce, Ce.exports)), Ce.exports;
}
var On, Cl;
function xp() {
  if (Cl) return On;
  Cl = 1;
  function a(t, e) {
    var s = -1, i = t.length;
    for (e || (e = Array(i)); ++s < i; )
      e[s] = t[s];
    return e;
  }
  return On = a, On;
}
var qn, El;
function gp() {
  if (El) return qn;
  El = 1;
  function a(t, e) {
    for (var s = -1, i = t == null ? 0 : t.length, n = 0, r = []; ++s < i; ) {
      var o = t[s];
      e(o, s, t) && (r[n++] = o);
    }
    return r;
  }
  return qn = a, qn;
}
var $n, Bl;
function mh() {
  if (Bl) return $n;
  Bl = 1;
  function a() {
    return [];
  }
  return $n = a, $n;
}
var tr, Ul;
function aa() {
  if (Ul) return tr;
  Ul = 1;
  var a = gp(), t = mh(), e = Object.prototype, s = e.propertyIsEnumerable, i = Object.getOwnPropertySymbols, n = i ? function(r) {
    return r == null ? [] : (r = Object(r), a(i(r), function(o) {
      return s.call(r, o);
    }));
  } : t;
  return tr = n, tr;
}
var er, Nl;
function Rp() {
  if (Nl) return er;
  Nl = 1;
  var a = _s(), t = aa();
  function e(s, i) {
    return a(s, t(s), i);
  }
  return er = e, er;
}
var sr, _l;
function fh() {
  if (_l) return sr;
  _l = 1;
  function a(t, e) {
    for (var s = -1, i = e.length, n = t.length; ++s < i; )
      t[n + s] = e[s];
    return t;
  }
  return sr = a, sr;
}
var ir, Hl;
function bh() {
  if (Hl) return ir;
  Hl = 1;
  var a = dh(), t = a(Object.getPrototypeOf, Object);
  return ir = t, ir;
}
var nr, Yl;
function yh() {
  if (Yl) return nr;
  Yl = 1;
  var a = fh(), t = bh(), e = aa(), s = mh(), i = Object.getOwnPropertySymbols, n = i ? function(r) {
    for (var o = []; r; )
      a(o, e(r)), r = t(r);
    return o;
  } : s;
  return nr = n, nr;
}
var rr, Pl;
function Sp() {
  if (Pl) return rr;
  Pl = 1;
  var a = _s(), t = yh();
  function e(s, i) {
    return a(s, t(s), i);
  }
  return rr = e, rr;
}
var ar, Al;
function xh() {
  if (Al) return ar;
  Al = 1;
  var a = fh(), t = ta();
  function e(s, i, n) {
    var r = i(s);
    return t(s) ? r : a(r, n(s));
  }
  return ar = e, ar;
}
var or, Kl;
function Zp() {
  if (Kl) return or;
  Kl = 1;
  var a = xh(), t = aa(), e = na();
  function s(i) {
    return a(i, e, t);
  }
  return or = s, or;
}
var lr, Jl;
function Gp() {
  if (Jl) return lr;
  Jl = 1;
  var a = xh(), t = yh(), e = ra();
  function s(i) {
    return a(i, e, t);
  }
  return lr = s, lr;
}
var cr, Dl;
function Vp() {
  if (Dl) return cr;
  Dl = 1;
  var a = ne(), t = Vt(), e = a(t, "DataView");
  return cr = e, cr;
}
var hr, Ql;
function Mp() {
  if (Ql) return hr;
  Ql = 1;
  var a = ne(), t = Vt(), e = a(t, "Promise");
  return hr = e, hr;
}
var ur, jl;
function Lp() {
  if (jl) return ur;
  jl = 1;
  var a = ne(), t = Vt(), e = a(t, "Set");
  return ur = e, ur;
}
var dr, Ol;
function wp() {
  if (Ol) return dr;
  Ol = 1;
  var a = ne(), t = Vt(), e = a(t, "WeakMap");
  return dr = e, dr;
}
var pr, ql;
function oa() {
  if (ql) return pr;
  ql = 1;
  var a = Vp(), t = $r(), e = Mp(), s = Lp(), i = wp(), n = Bs(), r = ah(), o = "[object Map]", l = "[object Object]", c = "[object Promise]", h = "[object Set]", u = "[object WeakMap]", d = "[object DataView]", p = r(a), m = r(t), f = r(e), b = r(s), y = r(i), x = n;
  return (a && x(new a(new ArrayBuffer(1))) != d || t && x(new t()) != o || e && x(e.resolve()) != c || s && x(new s()) != h || i && x(new i()) != u) && (x = function(g) {
    var S = n(g), Z = S == l ? g.constructor : void 0, V = Z ? r(Z) : "";
    if (V)
      switch (V) {
        case p:
          return d;
        case m:
          return o;
        case f:
          return c;
        case b:
          return h;
        case y:
          return u;
      }
    return S;
  }), pr = x, pr;
}
var mr, $l;
function vp() {
  if ($l) return mr;
  $l = 1;
  var a = Object.prototype, t = a.hasOwnProperty;
  function e(s) {
    var i = s.length, n = new s.constructor(i);
    return i && typeof s[0] == "string" && t.call(s, "index") && (n.index = s.index, n.input = s.input), n;
  }
  return mr = e, mr;
}
var fr, tc;
function Fp() {
  if (tc) return fr;
  tc = 1;
  var a = Vt(), t = a.Uint8Array;
  return fr = t, fr;
}
var br, ec;
function la() {
  if (ec) return br;
  ec = 1;
  var a = Fp();
  function t(e) {
    var s = new e.constructor(e.byteLength);
    return new a(s).set(new a(e)), s;
  }
  return br = t, br;
}
var yr, sc;
function kp() {
  if (sc) return yr;
  sc = 1;
  var a = la();
  function t(e, s) {
    var i = s ? a(e.buffer) : e.buffer;
    return new e.constructor(i, e.byteOffset, e.byteLength);
  }
  return yr = t, yr;
}
var xr, ic;
function Tp() {
  if (ic) return xr;
  ic = 1;
  var a = /\w*$/;
  function t(e) {
    var s = new e.constructor(e.source, a.exec(e));
    return s.lastIndex = e.lastIndex, s;
  }
  return xr = t, xr;
}
var gr, nc;
function Xp() {
  if (nc) return gr;
  nc = 1;
  var a = qr(), t = a ? a.prototype : void 0, e = t ? t.valueOf : void 0;
  function s(i) {
    return e ? Object(e.call(i)) : {};
  }
  return gr = s, gr;
}
var Rr, rc;
function zp() {
  if (rc) return Rr;
  rc = 1;
  var a = la();
  function t(e, s) {
    var i = s ? a(e.buffer) : e.buffer;
    return new e.constructor(i, e.byteOffset, e.length);
  }
  return Rr = t, Rr;
}
var Sr, ac;
function Wp() {
  if (ac) return Sr;
  ac = 1;
  var a = la(), t = kp(), e = Tp(), s = Xp(), i = zp(), n = "[object Boolean]", r = "[object Date]", o = "[object Map]", l = "[object Number]", c = "[object RegExp]", h = "[object Set]", u = "[object String]", d = "[object Symbol]", p = "[object ArrayBuffer]", m = "[object DataView]", f = "[object Float32Array]", b = "[object Float64Array]", y = "[object Int8Array]", x = "[object Int16Array]", g = "[object Int32Array]", S = "[object Uint8Array]", Z = "[object Uint8ClampedArray]", V = "[object Uint16Array]", G = "[object Uint32Array]";
  function M(L, k, w) {
    var E = L.constructor;
    switch (k) {
      case p:
        return a(L);
      case n:
      case r:
        return new E(+L);
      case m:
        return t(L, w);
      case f:
      case b:
      case y:
      case x:
      case g:
      case S:
      case Z:
      case V:
      case G:
        return i(L, w);
      case o:
        return new E();
      case l:
      case u:
        return new E(L);
      case c:
        return e(L);
      case h:
        return new E();
      case d:
        return s(L);
    }
  }
  return Sr = M, Sr;
}
var Zr, oc;
function Ip() {
  if (oc) return Zr;
  oc = 1;
  var a = Je(), t = Object.create, e = /* @__PURE__ */ function() {
    function s() {
    }
    return function(i) {
      if (!a(i))
        return {};
      if (t)
        return t(i);
      s.prototype = i;
      var n = new s();
      return s.prototype = void 0, n;
    };
  }();
  return Zr = e, Zr;
}
var Gr, lc;
function Cp() {
  if (lc) return Gr;
  lc = 1;
  var a = Ip(), t = bh(), e = ia();
  function s(i) {
    return typeof i.constructor == "function" && !e(i) ? a(t(i)) : {};
  }
  return Gr = s, Gr;
}
var Vr, cc;
function Ep() {
  if (cc) return Vr;
  cc = 1;
  var a = oa(), t = De(), e = "[object Map]";
  function s(i) {
    return t(i) && a(i) == e;
  }
  return Vr = s, Vr;
}
var Mr, hc;
function Bp() {
  if (hc) return Mr;
  hc = 1;
  var a = Ep(), t = ea(), e = sa(), s = e && e.isMap, i = s ? t(s) : a;
  return Mr = i, Mr;
}
var Lr, uc;
function Up() {
  if (uc) return Lr;
  uc = 1;
  var a = oa(), t = De(), e = "[object Set]";
  function s(i) {
    return t(i) && a(i) == e;
  }
  return Lr = s, Lr;
}
var wr, dc;
function Np() {
  if (dc) return wr;
  dc = 1;
  var a = Up(), t = ea(), e = sa(), s = e && e.isSet, i = s ? t(s) : a;
  return wr = i, wr;
}
var vr, pc;
function _p() {
  if (pc) return vr;
  pc = 1;
  var a = ep(), t = sp(), e = lh(), s = pp(), i = bp(), n = yp(), r = xp(), o = Rp(), l = Sp(), c = Zp(), h = Gp(), u = oa(), d = vp(), p = Wp(), m = Cp(), f = ta(), b = ch(), y = Bp(), x = Je(), g = Np(), S = na(), Z = ra(), V = 1, G = 2, M = 4, L = "[object Arguments]", k = "[object Array]", w = "[object Boolean]", E = "[object Date]", F = "[object Error]", X = "[object Function]", I = "[object GeneratorFunction]", H = "[object Map]", ot = "[object Number]", $ = "[object Object]", N = "[object RegExp]", _ = "[object Set]", K = "[object String]", Et = "[object Symbol]", re = "[object WeakMap]", Hs = "[object ArrayBuffer]", je = "[object DataView]", Zh = "[object Float32Array]", Gh = "[object Float64Array]", Vh = "[object Int8Array]", Mh = "[object Int16Array]", Lh = "[object Int32Array]", wh = "[object Uint8Array]", vh = "[object Uint8ClampedArray]", Fh = "[object Uint16Array]", kh = "[object Uint32Array]", C = {};
  C[L] = C[k] = C[Hs] = C[je] = C[w] = C[E] = C[Zh] = C[Gh] = C[Vh] = C[Mh] = C[Lh] = C[H] = C[ot] = C[$] = C[N] = C[_] = C[K] = C[Et] = C[wh] = C[vh] = C[Fh] = C[kh] = !0, C[F] = C[X] = C[re] = !1;
  function Oe(W, ae, oe, Th, qe, Bt) {
    var st, $e = ae & V, ts = ae & G, Xh = ae & M;
    if (oe && (st = qe ? oe(W, Th, qe, Bt) : oe(W)), st !== void 0)
      return st;
    if (!x(W))
      return W;
    var da = f(W);
    if (da) {
      if (st = d(W), !$e)
        return r(W, st);
    } else {
      var le = u(W), pa = le == X || le == I;
      if (b(W))
        return n(W, $e);
      if (le == $ || le == L || pa && !qe) {
        if (st = ts || pa ? {} : m(W), !$e)
          return ts ? l(W, i(st, W)) : o(W, s(st, W));
      } else {
        if (!C[le])
          return qe ? W : {};
        st = p(W, le, $e);
      }
    }
    Bt || (Bt = new a());
    var ma = Bt.get(W);
    if (ma)
      return ma;
    Bt.set(W, st), g(W) ? W.forEach(function(Ut) {
      st.add(Oe(Ut, ae, oe, Ut, W, Bt));
    }) : y(W) && W.forEach(function(Ut, Dt) {
      st.set(Dt, Oe(Ut, ae, oe, Dt, W, Bt));
    });
    var zh = Xh ? ts ? h : c : ts ? Z : S, fa = da ? void 0 : zh(W);
    return t(fa || W, function(Ut, Dt) {
      fa && (Dt = Ut, Ut = W[Dt]), e(st, Dt, Oe(Ut, ae, oe, Dt, W, Bt));
    }), st;
  }
  return vr = Oe, vr;
}
var Fr, mc;
function Hp() {
  if (mc) return Fr;
  mc = 1;
  var a = _p(), t = 1, e = 4;
  function s(i) {
    return a(i, t | e);
  }
  return Fr = s, Fr;
}
var Yp = Hp();
const fc = /* @__PURE__ */ Jr(Yp), ca = (a) => {
  const t = a.blocks.reduce((s, i) => (s[i.name] = i, s), {}), e = (s, i) => {
    let n = [];
    return s.forEach((r) => {
      if (r.type === "INSERT") {
        const o = r, l = t[o.block];
        if (!l) {
          ie.error("no block found for insert. block:", o.block);
          return;
        }
        const c = o.rowCount ?? 1, h = o.columnCount ?? 1, u = o.rowSpacing ?? 0, d = o.columnSpacing ?? 0, p = o.rotation ?? 0;
        let m, f;
        if (c > 1 || h > 1) {
          const b = Math.cos(p * Math.PI / 180), y = Math.sin(p * Math.PI / 180);
          m = { x: -y * u, y: b * u }, f = { x: b * d, y: y * d };
        } else
          m = { x: 0, y: 0 }, f = { x: 0, y: 0 };
        for (let b = 0; b < c; b++)
          for (let y = 0; y < h; y++) {
            const x = {
              x: o.x + m.x * b + f.x * y,
              y: o.y + m.y * b + f.y * y,
              scaleX: o.scaleX,
              scaleY: o.scaleY,
              scaleZ: o.scaleZ,
              extrusionX: o.extrusionX,
              extrusionY: o.extrusionY,
              extrusionZ: o.extrusionZ,
              rotation: o.rotation
            }, g = i.slice(0);
            g.push(x);
            const S = l.entities.map((Z) => {
              const V = fc(Z);
              switch (V.layer = o.layer, V.type) {
                case "LINE": {
                  V.start.x -= l.x, V.start.y -= l.y, V.end.x -= l.x, V.end.y -= l.y;
                  break;
                }
                case "LWPOLYLINE":
                case "POLYLINE": {
                  V.vertices.forEach((G) => {
                    G.x -= l.x, G.y -= l.y;
                  });
                  break;
                }
                case "CIRCLE":
                case "ELLIPSE":
                case "ARC": {
                  V.x -= l.x, V.y -= l.y;
                  break;
                }
                case "SPLINE": {
                  V.controlPoints.forEach((G) => {
                    G.x -= l.x, G.y -= l.y;
                  });
                  break;
                }
              }
              return V;
            });
            n = n.concat(e(S, g));
          }
      } else {
        const o = fc(r);
        o.transforms = i.slice().reverse(), n.push(o);
      }
    }), n;
  };
  return e(a.entities, []);
};
var O = Tc();
const Pp = (a, t) => typeof t > "u" || +t == 0 ? Math.round(a) : (a = +a, t = +t, isNaN(a) || !(typeof t == "number" && t % 1 === 0) ? NaN : (a = a.toString().split("e"), a = Math.round(+(a[0] + "e" + (a[1] ? +a[1] - t : -t))), a = a.toString().split("e"), +(a[0] + "e" + (a[1] ? +a[1] + t : t)))), Ap = (a, t, e, s, i) => {
  const n = e.length, r = e[0].length;
  if (a < 0 || a > 1)
    throw new Error("t out of bounds [0,1]: " + a);
  if (t < 1) throw new Error("degree must be at least 1 (linear)");
  if (t > n - 1)
    throw new Error("degree must be less than or equal to point count - 1");
  if (!i) {
    i = [];
    for (let m = 0; m < n; m++)
      i[m] = 1;
  }
  if (s) {
    if (s.length !== n + t + 1)
      throw new Error("bad knot vector length");
  } else {
    s = [];
    for (let m = 0; m < n + t + 1; m++)
      s[m] = m;
  }
  const o = [t, s.length - 1 - t], l = s[o[0]], c = s[o[1]];
  a = a * (c - l) + l, a = Math.max(a, l), a = Math.min(a, c);
  let h;
  for (h = o[0]; h < o[1] && !(a >= s[h] && a <= s[h + 1]); h++)
    ;
  const u = [];
  for (let m = 0; m < n; m++) {
    u[m] = [];
    for (let f = 0; f < r; f++)
      u[m][f] = e[m][f] * i[m];
    u[m][r] = i[m];
  }
  let d;
  for (let m = 1; m <= t + 1; m++)
    for (let f = h; f > h - t - 1 + m; f--) {
      d = (a - s[f]) / (s[f + t + 1 - m] - s[f]);
      for (let b = 0; b < r + 1; b++)
        u[f][b] = (1 - d) * u[f - 1][b] + d * u[f][b];
    }
  const p = [];
  for (let m = 0; m < r; m++)
    p[m] = Pp(u[h][m] / u[h][r], -9);
  return p;
}, Kp = (a, t, e, s) => {
  s || (s = 5);
  let i, n, r;
  e < 0 ? (i = Math.atan(-e) * 4, n = new O.V2(a[0], a[1]), r = new O.V2(t[0], t[1])) : (i = Math.atan(e) * 4, n = new O.V2(t[0], t[1]), r = new O.V2(a[0], a[1]));
  const o = r.sub(n), l = o.length(), c = n.add(o.multiply(0.5)), h = Math.abs(l / 2 / Math.tan(i / 2)), u = o.norm();
  let d;
  if (i < Math.PI) {
    const g = new O.V2(
      u.x * Math.cos(Math.PI / 2) - u.y * Math.sin(Math.PI / 2),
      u.y * Math.cos(Math.PI / 2) + u.x * Math.sin(Math.PI / 2)
    );
    d = c.add(g.multiply(-h));
  } else {
    const g = new O.V2(
      u.x * Math.cos(Math.PI / 2) - u.y * Math.sin(Math.PI / 2),
      u.y * Math.cos(Math.PI / 2) + u.x * Math.sin(Math.PI / 2)
    );
    d = c.add(g.multiply(h));
  }
  const p = Math.atan2(r.y - d.y, r.x - d.x) / Math.PI * 180;
  let m = Math.atan2(n.y - d.y, n.x - d.x) / Math.PI * 180;
  m < p && (m += 360);
  const f = r.sub(d).length(), b = Math.floor(p / s) * s + s, y = Math.ceil(m / s) * s - s, x = [];
  for (let g = b; g <= y; g += s)
    x.push(
      d.add(
        new O.V2(
          Math.cos(g / 180 * Math.PI) * f,
          Math.sin(g / 180 * Math.PI) * f
        )
      )
    );
  return e < 0 && x.reverse(), x.map((g) => [g.x, g.y]);
}, Jp = (a, t) => a.map(function(e) {
  return [
    e[0] * Math.cos(t) - e[1] * Math.sin(t),
    e[1] * Math.cos(t) + e[0] * Math.sin(t)
  ];
}), kr = (a, t, e, s, i, n, r) => {
  n < i && (n += Math.PI * 2);
  let o = [];
  const l = Math.PI * 2 / 72, c = 1e-6;
  for (let h = i; h < n - c; h += l)
    o.push([Math.cos(h) * e, Math.sin(h) * s]);
  return o.push([Math.cos(n) * e, Math.sin(n) * s]), r && (o = Jp(o, r)), o = o.map(function(h) {
    return [a + h[0], t + h[1]];
  }), o;
}, Dp = (a, t, e, s, i) => {
  const n = [], r = a.map(function(c) {
    return [c.x, c.y];
  }), o = [e[t]], l = [e[t], e[e.length - 1 - t]];
  for (let c = t + 1; c < e.length - t; ++c)
    o[o.length - 1] !== e[c] && o.push(e[c]);
  s = s || 25;
  for (let c = 1; c < o.length; ++c) {
    const h = o[c - 1], u = o[c];
    for (let d = 0; d <= s; ++d) {
      let m = (d / s * (u - h) + h - l[0]) / (l[1] - l[0]);
      m = Math.max(m, 0), m = Math.min(m, 1);
      const f = Ap(m, t, r, e, i);
      n.push(f);
    }
  }
  return n;
}, Qp = (a) => {
  const t = [], e = [];
  for (const n of a.vertices)
    if (n.faces) {
      const r = { indices: [], hiddens: [] };
      for (const o of n.faces) {
        if (o === 0)
          break;
        r.indices.push(o < 0 ? -o - 1 : o - 1), r.hiddens.push(o < 0);
      }
      [3, 4].includes(r.indices.length) && e.push(r);
    } else
      t.push({ x: n.x, y: n.y });
  const s = [], i = (n, r) => {
    for (const o of s)
      if (o.slice(-1)[0] === n)
        return o.push(r);
    s.push([n, r]);
  };
  for (const n of e)
    for (let r = 0; r < n.indices.length; r++) {
      if (n.hiddens[r])
        continue;
      const o = (r + 1) % n.indices.length;
      i(n.indices[r], n.indices[o]);
    }
  for (const n of s)
    for (const r of s)
      if (n !== r && n[0] === r.slice(-1)[0]) {
        r.push(...n.slice(1)), n.splice(0, n.length);
        break;
      }
  return s.filter((n) => n.length).map((n) => n.map((r) => t[r]).map((r) => [r.x, r.y]));
}, gh = (a, t) => {
  t = t || {};
  let e;
  if (a.type === "LINE" && (e = [
    [a.start.x, a.start.y],
    [a.end.x, a.end.y]
  ]), a.type === "LWPOLYLINE" || a.type === "POLYLINE") {
    if (e = [], a.polyfaceMesh)
      e.push(...Qp(a)[0]);
    else if (!a.polygonMesh) {
      if (a.vertices.length) {
        a.closed && (a.vertices = a.vertices.concat(a.vertices[0]));
        for (let s = 0, i = a.vertices.length; s < i - 1; ++s) {
          const n = [a.vertices[s].x, a.vertices[s].y], r = [a.vertices[s + 1].x, a.vertices[s + 1].y];
          e.push(n), a.vertices[s].bulge && (e = e.concat(
            Kp(n, r, a.vertices[s].bulge)
          )), s === i - 2 && e.push(r);
        }
      }
    }
  }
  if (a.type === "CIRCLE" && (e = kr(
    a.x,
    a.y,
    a.r,
    a.r,
    0,
    Math.PI * 2
  ), a.extrusionZ === -1 && (e = e.map(function(s) {
    return [-s[0], s[1]];
  }))), a.type === "ELLIPSE") {
    const s = Math.sqrt(
      a.majorX * a.majorX + a.majorY * a.majorY
    ), i = a.axisRatio * s, n = -Math.atan2(-a.majorY, a.majorX);
    e = kr(
      a.x,
      a.y,
      s,
      i,
      a.startAngle,
      a.endAngle,
      n
    ), a.extrusionZ === -1 && (e = e.map(function(r) {
      return [-r[0], r[1]];
    }));
  }
  return a.type === "ARC" && (e = kr(
    a.x,
    a.y,
    a.r,
    a.r,
    a.startAngle,
    a.endAngle,
    void 0
  ), a.extrusionZ === -1 && (e = e.map(function(s) {
    return [-s[0], s[1]];
  }))), a.type === "SPLINE" && (e = Dp(
    a.controlPoints,
    a.degree,
    a.knots,
    t.interpolationsPerSplineSegment,
    a.weights
  )), e || (ie.warn("unsupported entity for converting to polyline:", a.type), []);
}, Cr = [
  [0, 0, 0],
  [255, 0, 0],
  [255, 255, 0],
  [0, 255, 0],
  [0, 255, 255],
  [0, 0, 255],
  [255, 0, 255],
  [255, 255, 255],
  [65, 65, 65],
  [128, 128, 128],
  [255, 0, 0],
  [255, 170, 170],
  [189, 0, 0],
  [189, 126, 126],
  [129, 0, 0],
  [129, 86, 86],
  [104, 0, 0],
  [104, 69, 69],
  [79, 0, 0],
  [79, 53, 53],
  [255, 63, 0],
  [255, 191, 170],
  [189, 46, 0],
  [189, 141, 126],
  [129, 31, 0],
  [129, 96, 86],
  [104, 25, 0],
  [104, 78, 69],
  [79, 19, 0],
  [79, 59, 53],
  [255, 127, 0],
  [255, 212, 170],
  [189, 94, 0],
  [189, 157, 126],
  [129, 64, 0],
  [129, 107, 86],
  [104, 52, 0],
  [104, 86, 69],
  [79, 39, 0],
  [79, 66, 53],
  [255, 191, 0],
  [255, 234, 170],
  [189, 141, 0],
  [189, 173, 126],
  [129, 96, 0],
  [129, 118, 86],
  [104, 78, 0],
  [104, 95, 69],
  [79, 59, 0],
  [79, 73, 53],
  [255, 255, 0],
  [255, 255, 170],
  [189, 189, 0],
  [189, 189, 126],
  [129, 129, 0],
  [129, 129, 86],
  [104, 104, 0],
  [104, 104, 69],
  [79, 79, 0],
  [79, 79, 53],
  [191, 255, 0],
  [234, 255, 170],
  [141, 189, 0],
  [173, 189, 126],
  [96, 129, 0],
  [118, 129, 86],
  [78, 104, 0],
  [95, 104, 69],
  [59, 79, 0],
  [73, 79, 53],
  [127, 255, 0],
  [212, 255, 170],
  [94, 189, 0],
  [157, 189, 126],
  [64, 129, 0],
  [107, 129, 86],
  [52, 104, 0],
  [86, 104, 69],
  [39, 79, 0],
  [66, 79, 53],
  [63, 255, 0],
  [191, 255, 170],
  [46, 189, 0],
  [141, 189, 126],
  [31, 129, 0],
  [96, 129, 86],
  [25, 104, 0],
  [78, 104, 69],
  [19, 79, 0],
  [59, 79, 53],
  [0, 255, 0],
  [170, 255, 170],
  [0, 189, 0],
  [126, 189, 126],
  [0, 129, 0],
  [86, 129, 86],
  [0, 104, 0],
  [69, 104, 69],
  [0, 79, 0],
  [53, 79, 53],
  [0, 255, 63],
  [170, 255, 191],
  [0, 189, 46],
  [126, 189, 141],
  [0, 129, 31],
  [86, 129, 96],
  [0, 104, 25],
  [69, 104, 78],
  [0, 79, 19],
  [53, 79, 59],
  [0, 255, 127],
  [170, 255, 212],
  [0, 189, 94],
  [126, 189, 157],
  [0, 129, 64],
  [86, 129, 107],
  [0, 104, 52],
  [69, 104, 86],
  [0, 79, 39],
  [53, 79, 66],
  [0, 255, 191],
  [170, 255, 234],
  [0, 189, 141],
  [126, 189, 173],
  [0, 129, 96],
  [86, 129, 118],
  [0, 104, 78],
  [69, 104, 95],
  [0, 79, 59],
  [53, 79, 73],
  [0, 255, 255],
  [170, 255, 255],
  [0, 189, 189],
  [126, 189, 189],
  [0, 129, 129],
  [86, 129, 129],
  [0, 104, 104],
  [69, 104, 104],
  [0, 79, 79],
  [53, 79, 79],
  [0, 191, 255],
  [170, 234, 255],
  [0, 141, 189],
  [126, 173, 189],
  [0, 96, 129],
  [86, 118, 129],
  [0, 78, 104],
  [69, 95, 104],
  [0, 59, 79],
  [53, 73, 79],
  [0, 127, 255],
  [170, 212, 255],
  [0, 94, 189],
  [126, 157, 189],
  [0, 64, 129],
  [86, 107, 129],
  [0, 52, 104],
  [69, 86, 104],
  [0, 39, 79],
  [53, 66, 79],
  [0, 63, 255],
  [170, 191, 255],
  [0, 46, 189],
  [126, 141, 189],
  [0, 31, 129],
  [86, 96, 129],
  [0, 25, 104],
  [69, 78, 104],
  [0, 19, 79],
  [53, 59, 79],
  [0, 0, 255],
  [170, 170, 255],
  [0, 0, 189],
  [126, 126, 189],
  [0, 0, 129],
  [86, 86, 129],
  [0, 0, 104],
  [69, 69, 104],
  [0, 0, 79],
  [53, 53, 79],
  [63, 0, 255],
  [191, 170, 255],
  [46, 0, 189],
  [141, 126, 189],
  [31, 0, 129],
  [96, 86, 129],
  [25, 0, 104],
  [78, 69, 104],
  [19, 0, 79],
  [59, 53, 79],
  [127, 0, 255],
  [212, 170, 255],
  [94, 0, 189],
  [157, 126, 189],
  [64, 0, 129],
  [107, 86, 129],
  [52, 0, 104],
  [86, 69, 104],
  [39, 0, 79],
  [66, 53, 79],
  [191, 0, 255],
  [234, 170, 255],
  [141, 0, 189],
  [173, 126, 189],
  [96, 0, 129],
  [118, 86, 129],
  [78, 0, 104],
  [95, 69, 104],
  [59, 0, 79],
  [73, 53, 79],
  [255, 0, 255],
  [255, 170, 255],
  [189, 0, 189],
  [189, 126, 189],
  [129, 0, 129],
  [129, 86, 129],
  [104, 0, 104],
  [104, 69, 104],
  [79, 0, 79],
  [79, 53, 79],
  [255, 0, 191],
  [255, 170, 234],
  [189, 0, 141],
  [189, 126, 173],
  [129, 0, 96],
  [129, 86, 118],
  [104, 0, 78],
  [104, 69, 95],
  [79, 0, 59],
  [79, 53, 73],
  [255, 0, 127],
  [255, 170, 212],
  [189, 0, 94],
  [189, 126, 157],
  [129, 0, 64],
  [129, 86, 107],
  [104, 0, 52],
  [104, 69, 86],
  [79, 0, 39],
  [79, 53, 66],
  [255, 0, 63],
  [255, 170, 191],
  [189, 0, 46],
  [189, 126, 141],
  [129, 0, 31],
  [129, 86, 96],
  [104, 0, 25],
  [104, 69, 78],
  [79, 0, 19],
  [79, 53, 59],
  [51, 51, 51],
  [80, 80, 80],
  [105, 105, 105],
  [130, 130, 130],
  [190, 190, 190],
  [255, 255, 255]
], jp = (a, t) => {
  const e = a[t.layer];
  if (e) {
    const i = "colorNumber" in t && t.colorNumber !== 256 ? t.colorNumber : e.colorNumber, n = Cr[i];
    return n || [0, 0, 0];
  } else
    return ie.warn("no layer table for layer:" + t.layer), [0, 0, 0];
}, bc = (a, t) => ({
  x: a.x * Math.cos(t) - a.y * Math.sin(t),
  y: a.y * Math.cos(t) + a.x * Math.sin(t)
}), Op = (a) => a[0] === 255 && a[1] === 255 && a[2] === 255 ? "rgb(0, 0, 0)" : `rgb(${a[0]}, ${a[1]}, ${a[2]})`, qp = (a, t, e, s) => {
  const i = e, n = t, r = t.length;
  let o = 0, l = !1;
  for (let d = 0; d < r + a; d++)
    if (s > i[d] && s <= i[d + 1]) {
      o = d, l = !0;
      break;
    }
  if (!l)
    throw new Error("invalid new knot");
  const c = [];
  for (let d = 0; d < r + a + 1; d++)
    d <= o ? c[d] = i[d] : d === o + 1 ? c[d] = s : c[d] = i[d - 1];
  let h;
  const u = [];
  for (let d = 0; d < r + 1; d++)
    d <= o - a + 1 ? h = 1 : o - a + 2 <= d && d <= o ? i[d + a - 1] - i[d] === 0 ? h = 0 : h = (s - i[d]) / (i[d + a - 1] - i[d]) : h = 0, h === 0 ? u[d] = n[d - 1] : h === 1 ? u[d] = n[d] : u[d] = {
      x: (1 - h) * n[d - 1].x + h * n[d].x,
      y: (1 - h) * n[d - 1].y + h * n[d].y
    };
  return { controlPoints: u, knots: c };
}, $p = (a, t) => {
  for (let e = 1; e < a; ++e)
    if (t[e] !== t[0])
      throw Error(`not pinned. order: ${a} knots: ${t}`);
  for (let e = t.length - 2; e > t.length - a - 1; --e)
    if (t[e] !== t[t.length - 1])
      throw Error(`not pinned. order: ${a} knots: ${t}`);
}, Rh = (a, t) => {
  let e = 1;
  for (let s = t + 1; s < a.length && a[s] === a[t]; ++s)
    ++e;
  return e;
}, tm = (a, t) => {
  const e = [];
  let s = a;
  for (; s < t.length - a; ) {
    const i = t[s], n = Rh(t, s);
    for (let r = 0; r < a - n - 1; ++r)
      e.push(i);
    s = s + n;
  }
  return e;
}, em = (a, t, e) => ($p(a, e), tm(a, e).reduce(
  (i, n) => qp(a, i.controlPoints, i.knots, n),
  { controlPoints: t, knots: e }
)), Qe = (a, t, e) => {
  let s = "";
  const i = e.map((r) => {
    const o = r.x || 0, l = r.y || 0, c = r.scaleX || 1, h = r.scaleY || 1, u = (r.rotation || 0) / 180 * Math.PI, { cos: d, sin: p } = Math;
    let m, f, b, y, x, g;
    return r.extrusionZ === -1 ? (m = -c * d(u), f = c * p(u), b = h * p(u), y = h * d(u), x = -o, g = l) : (m = c * d(u), f = c * p(u), b = -h * p(u), y = h * d(u), x = o, g = l), [m, f, b, y, x, g];
  });
  let n = new O.Box2();
  if (a.valid) {
    let r = [
      { x: a.min.x, y: a.min.y },
      { x: a.max.x, y: a.min.y },
      { x: a.max.x, y: a.max.y },
      { x: a.min.x, y: a.max.y }
    ];
    i.forEach(([o, l, c, h, u, d]) => {
      r = r.map((p) => ({
        x: p.x * o + p.y * c + u,
        y: p.x * l + p.y * h + d
      }));
    }), n = r.reduce((o, l) => o.expandByPoint(l), new O.Box2());
  }
  return i.reverse(), i.forEach(([r, o, l, c, h, u]) => {
    s += `<g transform="matrix(${r} ${o} ${l} ${c} ${h} ${u})">`;
  }), s += t, i.forEach((r) => {
    s += "</g>";
  }), { bbox: n, element: s };
}, ha = (a, { bbox: t, element: e }) => a.extrusionZ === -1 ? {
  bbox: new O.Box2().expandByPoint({ x: -t.min.x, y: t.min.y }).expandByPoint({ x: -t.max.x, y: t.max.y }),
  element: `<g transform="matrix(-1 0 0 1 0 0)">
        ${e}
      </g>`
} : { bbox: t, element: e }, Tr = (a) => {
  const t = gh(a), e = t.reduce(
    (i, [n, r]) => i.expandByPoint({ x: n, y: r }),
    new O.Box2()
  ), s = t.reduce((i, n, r) => (i += r === 0 ? "M" : "L", i += n[0] + "," + n[1], i), "");
  return Qe(
    e,
    `<path d="${s}" />`,
    a.transforms
  );
}, sm = (a) => {
  const t = new O.Box2().expandByPoint({
    x: a.x + a.r,
    y: a.y + a.r
  }).expandByPoint({
    x: a.x - a.r,
    y: a.y - a.r
  }), e = `<circle cx="${a.x}" cy="${a.y}" r="${a.r}" />`, { bbox: s, element: i } = ha(a, {
    bbox: t,
    element: e
  });
  return Qe(s, i, a.transforms);
}, Sh = (a, t, e, s, i, n, r, o) => {
  const l = Math.sqrt(e * e + s * s), c = i * l, h = -Math.atan2(-s, e), u = im(
    a,
    t,
    e,
    s,
    i,
    n,
    r
  );
  if (Math.abs(n - r) < 1e-9 || Math.abs(n - r + Math.PI * 2) < 1e-9) {
    const d = `<g transform="rotate(${h / Math.PI * 180} ${a}, ${t})">
      <ellipse cx="${a}" cy="${t}" rx="${l}" ry="${c}" />
    </g>`;
    return { bbox: u, element: d };
  } else {
    const d = bc(
      {
        x: Math.cos(n) * l,
        y: Math.sin(n) * c
      },
      h
    ), p = {
      x: a + d.x,
      y: t + d.y
    }, m = bc(
      {
        x: Math.cos(r) * l,
        y: Math.sin(r) * c
      },
      h
    ), f = {
      x: a + m.x,
      y: t + m.y
    }, y = (r < n ? r + Math.PI * 2 : r) - n < Math.PI ? 0 : 1, g = `<path d="${`M ${p.x} ${p.y} A ${l} ${c} ${h / Math.PI * 180} ${y} 1 ${f.x} ${f.y}`}" />`;
    return { bbox: u, element: g };
  }
}, im = (a, t, e, s, i, n, r, o) => {
  for (; n < 0; ) n += Math.PI * 2;
  for (; r <= n; ) r += Math.PI * 2;
  const l = [];
  if (Math.abs(e) < 1e-12 || Math.abs(s) < 1e-12)
    for (let p = 0; p < 4; p++)
      l.push(p / 2 * Math.PI);
  else
    l[0] = Math.atan(-s * i / e) - Math.PI, l[1] = Math.atan(e * i / s) - Math.PI, l[2] = l[0] - Math.PI, l[3] = l[1] - Math.PI;
  for (let p = 4; p >= 0; p--) {
    for (; l[p] < n; ) l[p] += Math.PI * 2;
    l[p] > r && l.splice(p, 1);
  }
  l.push(n), l.push(r);
  const c = l.map((p) => ({
    x: Math.cos(p),
    y: Math.sin(p)
  })), h = [
    [e, -s * i],
    [s, e * i]
  ];
  return c.map((p) => ({
    x: p.x * h[0][0] + p.y * h[0][1] + a,
    y: p.x * h[1][0] + p.y * h[1][1] + t
  })).reduce((p, m) => (p.expandByPoint(m), p), new O.Box2());
}, nm = (a) => {
  const { bbox: t, element: e } = Sh(
    a.x,
    a.y,
    a.majorX,
    a.majorY,
    a.axisRatio,
    a.startAngle,
    a.endAngle
  ), { bbox: s, element: i } = ha(a, {
    bbox: t,
    element: e
  });
  return Qe(s, i, a.transforms);
}, rm = (a) => {
  const { bbox: t, element: e } = Sh(
    a.x,
    a.y,
    a.r,
    0,
    1,
    a.startAngle,
    a.endAngle,
    a.extrusionZ === -1
  ), { bbox: s, element: i } = ha(a, {
    bbox: t,
    element: e
  });
  return Qe(s, i, a.transforms);
}, am = (a, t, e) => {
  const s = [];
  let i = 0, n = a;
  for (; n < t.length - a + 1; ) {
    const r = Rh(t, n), o = e.slice(i, i + a);
    a === 4 ? s.push(
      `<path d="M ${o[0].x} ${o[0].y} C ${o[1].x} ${o[1].y} ${o[2].x} ${o[2].y} ${o[3].x} ${o[3].y}" />`
    ) : a === 3 && s.push(
      `<path d="M ${o[0].x} ${o[0].y} Q ${o[1].x} ${o[1].y} ${o[2].x} ${o[2].y}" />`
    ), i += r, n += r;
  }
  return s;
}, om = (a) => {
  let t = new O.Box2();
  a.controlPoints.forEach((r) => {
    t = t.expandByPoint(r);
  });
  const e = a.degree + 1, s = em(e, a.controlPoints, a.knots), n = `<g>${am(e, s.knots, s.controlPoints).join("")}</g>`;
  return Qe(t, n, a.transforms);
}, lm = (a) => {
  switch (a.type) {
    case "CIRCLE":
      return sm(a);
    case "ELLIPSE":
      return nm(a);
    case "ARC":
      return rm(a);
    case "SPLINE": {
      const t = a.weights && a.weights.some((e) => e !== 1);
      if ((a.degree === 2 || a.degree === 3) && !t)
        try {
          return om(a);
        } catch {
          return Tr(a);
        }
      else
        return Tr(a);
    }
    case "LINE":
    case "LWPOLYLINE":
    case "POLYLINE":
      return Tr(a);
    default:
      return ie.warn("entity type not supported in SVG rendering:", a.type), null;
  }
}, cm = (a) => {
  const t = ca(a), { bbox: e, elements: s } = t.reduce(
    (n, r, o) => {
      const l = jp(a.tables.layers, r), c = lm(r);
      if (c) {
        const { bbox: h, element: u } = c;
        h.valid && (n.bbox.expandByPoint(h.min), n.bbox.expandByPoint(h.max)), n.elements.push(
          `<g stroke="${Op(l)}">${u}</g>`
        );
      }
      return n;
    },
    {
      bbox: new O.Box2(),
      elements: []
    }
  ), i = e.valid ? {
    x: e.min.x,
    y: -e.max.y,
    width: e.max.x - e.min.x,
    height: e.max.y - e.min.y
  } : {
    x: 0,
    y: 0,
    width: 0,
    height: 0
  };
  return `<?xml version="1.0"?>
<svg
  xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
  preserveAspectRatio="xMinYMin meet"
  viewBox="${i.x} ${i.y} ${i.width} ${i.height}"
  width="100%" height="100%"
>
  <g stroke="#000000" stroke-width="0.1%" fill="none" transform="matrix(1,0,0,-1,0,0)">
    ${s.join(`
`)}
  </g>
</svg>`;
}, hm = (a, t) => (t.forEach((e) => {
  a = a.map(function(s) {
    let i = [s[0], s[1]];
    if (e.scaleX && (i[0] = i[0] * e.scaleX), e.scaleY && (i[1] = i[1] * e.scaleY), e.rotation) {
      const n = e.rotation / 180 * Math.PI;
      i = [
        i[0] * Math.cos(n) - i[1] * Math.sin(n),
        i[1] * Math.cos(n) + i[0] * Math.sin(n)
      ];
    }
    return e.x && (i[0] = i[0] + e.x), e.y && (i[1] = i[1] + e.y), e.extrusionZ === -1 && (i[0] = -i[0]), i;
  });
}), a), um = (a) => {
  const e = ca(a).map((i) => {
    const n = a.tables.layers[i.layer];
    let r = 0;
    return "colorNumber" in i ? r = i.colorNumber : n && (r = n.colorNumber), Cr[r] === void 0 && (r = 0), {
      rgb: Cr[r],
      layer: n,
      vertices: hm(gh(i), i.transforms)
    };
  }), s = new O.Box2();
  return e.forEach((i) => {
    i.vertices.forEach((n) => {
      s.expandByPoint({ x: n[0], y: n[1] });
    });
  }), { bbox: s, polylines: e };
}, dm = (a) => a.reduce((t, e) => {
  const s = e.layer;
  return t[s] || (t[s] = []), t[s].push(e), t;
}, {});
class pm {
  constructor(t) {
    if (typeof t != "string")
      throw Error("Helper constructor expects a DXF string");
    this._contents = t, this._parsed = null, this._denormalised = null;
  }
  parse() {
    return this._parsed = M0(this._contents), ie.info("parsed:", this.parsed), this._parsed;
  }
  get parsed() {
    return this._parsed === null && this.parse(), this._parsed;
  }
  denormalise() {
    return this._denormalised = ca(this.parsed), ie.info("denormalised:", this._denormalised), this._denormalised;
  }
  get denormalised() {
    return this._denormalised || this.denormalise(), this._denormalised;
  }
  group() {
    this._groups = dm(this.denormalised);
  }
  get groups() {
    return this._groups || this.group(), this._groups;
  }
  toSVG() {
    return cm(this.parsed);
  }
  toPolylines() {
    return um(this.parsed);
  }
}
class gm extends zs {
  constructor() {
    super(), this._cache = /* @__PURE__ */ new Map(), this.useCache = !1, this.colorHelper = new Kr(), this.LayerHelper = new kc();
  }
  /**
   * Returns a Three.js object with the dxf data. Calls to getFromPath method internally
   * @param file {File} dxf file to load.
   * @param fontPath {string} path to the font file.
      * @return {THREE.Group} object with the dxf data
  */
  async getFromFile(t, e) {
    let s = URL.createObjectURL(t);
    return await this.getFromPath(s, e);
  }
  /**
   * Returns a Three.js object with the dxf data.
   * @param path path to a dxf file to load. Type: String
   * @param fontPath path to the font file. Type: string	 
      * @return THREE.Group object with the dxf data
  */
  async getFromPath(t, e) {
    if (await this._loadFont(e), !this._font) return null;
    let s = this._fromCache(t);
    if (s)
      return this._drawDXF(s.data ? s.data : s);
    await this.trigger("progress", "Fetching file ...");
    let i = await fetch(t);
    if (i.status !== 200) return null;
    let n = await i.text();
    await this.trigger("progress", "Parsing file ...");
    let r = new pm(n), o = r.parse();
    return await this.trigger("progress", "Drawing file ..."), this.lastDXF = o, r._parsed = null, r = null, i = null, n = null, this.lastDXF.tables.layers = this.LayerHelper.parse(this.lastDXF.tables.layers), this.layers = this.lastDXF.tables.layers, this.unit = this.lastDXF.header ? this.lastDXF.header.insUnits : 0, await this._drawDXF(this.lastDXF);
  }
  async _drawDXF(t) {
    let e = new at();
    e.name = "DXFViewer", this.useCache || (P.cache = !1);
    let s = new Is(t);
    s.subscribe("log", async (d) => await this._log("LineEntity", d));
    let i = new Xc(t);
    i.subscribe("log", async (d) => await this._log("CircleEntity", d));
    let n = new jr(t);
    n.subscribe("log", async (d) => await this._log("SplineEntity", d));
    let r = new Qr(t);
    r.subscribe("log", async (d) => await this._log("SolidEntity", d));
    let o = new Rd(t, this._font);
    o.subscribe("log", async (d) => await this._log("DimensionEntity", d));
    let l = new zt(t, this._font);
    l.subscribe("log", async (d) => await this._log("TextEntity", d));
    let c = new Wc(t, this._font);
    c.subscribe("log", async (d) => await this._log("Ole2FrameEntity", d));
    let h = new Cc(t, this._font);
    h.subscribe("log", async (d) => await this._log("InsertEntity", d));
    let u = new zc(t, this._font);
    return u.subscribe("log", async (d) => await this._log("HatchEntity", d)), P.onBeforeTextDraw = this.onBeforeTextDraw, s = s.draw(t), i = i.draw(t), n = n.draw(t), r = r.draw(t), o = await o.draw(t), l = l.draw(t), c = await c.draw(t), h = await h.draw(t, t), u = u.draw(t, (d) => {
      const p = [], m = (f) => {
        f.forEach((b) => {
          d.includes(b.userData.entity.handle) && p.push(b);
        });
      };
      return s && m(s.children), i && m(i.children), n && m(n.children), r && m(r.children), o && m(o.children), l && m(l.children), c && m(c.children), h && m(h.children), p;
    }), s && e.add(s), i && e.add(i), n && e.add(n), r && e.add(r), o && e.add(o), l && e.add(l), c && e.add(c), h && e.add(h), u && e.add(u), this._rotateByView(e, t), e;
  }
  _toCache(t, e) {
    this._cache.set(this._replaceEspecialChars(t), new WeakRef(e));
  }
  _fromCache(t) {
    const e = this._replaceEspecialChars(t);
    if (this._cache.has(e)) {
      const s = this._cache.get(e).deref();
      if (s !== void 0)
        return s;
    }
    return null;
  }
  _replaceEspecialChars(t) {
    return t.replaceAll("/", "").replaceAll(".", "").replaceAll("_", "").replaceAll("-", "");
  }
  async _loadFont(t) {
    if (!this._font)
      try {
        this._font = await new Promise((e, s) => {
          new Hu().load(t, e, null, s);
        });
      } catch (e) {
        await this.trigger("error", e), this._font = null;
      }
  }
  _rotateByView(t, e) {
    let s = e.objects && e.objects.layouts ? e.objects.layouts.find((n) => n.name.toLowerCase() === "model") : null;
    if (!s) return;
    let i = Object.keys(e.tables.vports);
    for (let n = 0; n < i.length; n++) {
      let r = e.tables.vports[i[n]];
      if (r.handle === s.lastActiveViewport && r.angle !== 0) {
        t.rotateOnAxis(new R(0, 0, 1), r.angle * Math.PI / 180);
        return;
      }
    }
  }
  async _log(t, e) {
    await this.trigger("log", `${t}: ${e}`);
  }
  get DefaultTextHeight() {
    return zt.TextHeight;
  }
  set DefaultTextHeight(t) {
    zt.TextHeight = t;
  }
  get DefaultTextScale() {
    return zt.TextScale;
  }
  set DefaultTextScale(t) {
    zt.TextScale = t;
  }
}
class yc {
  constructor() {
    this.snaps = [], this.min = new R(1 / 0, 1 / 0, 1 / 0), this.max = new R(-1 / 0, -1 / 0, -1 / 0), this.cuts = 5, this.voxel = null, this.decimals = 6;
  }
  /**
   * Fills the snaps array with the points of the scene
   * @param objs {Array|THREE.Object3D} ThreeJS object or array of ThreeJS objects.
  */
  process(t) {
    t = t instanceof Array ? t : [t], this._getSnaps(t, !0, !1), this._getSize(t), this.voxel = this._generateVoxels(this.min, this.max, 1)[0], this.snaps.forEach((e) => this._addPointToVoxel(e, this.voxel.voxels));
  }
  /**
   * Finds the nearest voxel to the point
   * @param {ThreeJS.Vector3} point Point to check near voxels. Type: THREE.Vector3
  */
  findVoxel(t, e = null) {
    let s = {
      x: Number(t.x.toFixed(this.decimals)),
      y: Number(t.y.toFixed(this.decimals)),
      z: Number(t.z.toFixed(this.decimals))
    };
    e = e || this.voxel.voxels;
    for (let i = 0; i < e.length; i++) {
      let n = e[i];
      if (n.min.x <= s.x && s.x <= n.max.x && n.min.y <= s.y && s.y <= n.max.y && n.min.z <= s.z && s.z <= n.max.z)
        return n.voxels.length > 0 ? this.findVoxel(t, n.voxels) : n;
    }
    return null;
  }
  _getSnaps(t, e = !0, s = !0) {
    for (let i = 0; i < t.length; i++)
      t[i].traverse((r) => {
        if (e && (r.isLine || r.isLineSegments) || s && r.isMesh) {
          r.updateWorldMatrix(!0, !1);
          for (let o = 0; o < r.geometry.attributes.position.count; o++)
            this.snaps.push({
              entity: r,
              point: new R(
                r.geometry.attributes.position.getX(o),
                r.geometry.attributes.position.getY(o),
                r.geometry.attributes.position.getZ(o)
              ).applyMatrix4(r.matrixWorld)
            });
        }
      });
  }
  _generateVoxels(t, e, s) {
    if (s === this.cuts) return [];
    let i = [], n = e.clone().sub(t).multiplyScalar(1 / s);
    for (let r = 0; r < s; r++)
      for (let o = 0; o < s; o++)
        for (let l = 0; l < s; l++) {
          let c = {
            name: "VOXEL_" + r + "_" + o + "_" + l,
            min: new R(t.x + r * n.x, t.y + o * n.y, t.z + l * n.z),
            max: new R(t.x + (r + 1) * n.x, t.y + (o + 1) * n.y, t.z + (l + 1) * n.z),
            snaps: []
          };
          c.voxels = this._generateVoxels(c.min, c.max, s + 1), i.push(c);
        }
    return i;
  }
  _addPointToVoxel(t, e) {
    for (let s = 0; s < e.length; s++) {
      let i = e[s];
      if (i.min.x <= t.point.x && t.point.x <= i.max.x && i.min.y <= t.point.y && t.point.y <= i.max.y && i.min.z <= t.point.z && t.point.z <= i.max.z) {
        i.voxels.length === 0 ? i.snaps.push(t) : this._addPointToVoxel(t, i.voxels);
        return;
      }
    }
  }
  _getSize(t) {
    t.forEach((e) => {
      let s = new St().setFromObject(e);
      this.min.set(Math.min(this.min.x, s.min.x), Math.min(this.min.y, s.min.y), Math.min(this.min.z, s.min.z)), this.max.set(Math.max(this.max.x, s.max.x), Math.max(this.max.y, s.max.y), Math.max(this.max.z, s.max.z));
    });
  }
  /**
   * Clears all the data for a correct dispose of the object.
  */
  clear() {
    this.snaps.length = 0, this.min = null, this.max = null, this.voxel = null;
  }
}
class Rm extends zs {
  /**
   * Constructor
   * @param dxf {Array|THREE.Object3D} ThreeJS object or array of ThreeJS objects.
   * @param renderer {THREE.WebGLRenderer} ThreeJS renderer.
   * @param scene {THREE.Scene} ThreeJS scene.
   * @param camera {THREE.Camera} ThreeJS camera.
   * @param controls {THREE.OrbitControls} ThreeJS orbit controls.
  */
  constructor(t, e, s, i, n) {
    super(), this.container = e.domElement, this.scene = s, this.camera = i, this.controls = n, this.snaps = new yc(), this.snaps.process(t), this._mouseDownEvent = (r) => {
      this._mouseDown(r);
    }, this._mouseUpEvent = (r) => {
      this._mouseUp(r);
    }, this._mouseMoveEvent = (r) => {
      this._mouseMove(r);
    }, this.container.addEventListener("pointerdown", this._mouseDownEvent), this.container.addEventListener("pointerup", this._mouseUpEvent), this.container.addEventListener("pointermove", this._mouseMoveEvent), this.raycaster = new Fc(), this.mouse = new v(), this.plane = new iu(), this.planeNormal = new R(), this.mousePos = new R(), this.vectorHelper = new R(), document.addEventListener("wheel", () => {
      this._changeSquareSize = !0;
    });
  }
  _mouseDown() {
  }
  _mouseUp() {
  }
  async _mouseMove(t) {
    if (this.snaps) {
      t.mousePosInScene = this._getMousePosInScene(t);
      let e = { x: t.mousePosInScene.x, y: t.mousePosInScene.y, z: t.mousePosInScene.z }, s = this.snaps.findVoxel(e);
      if (s) {
        let i = this._findNearestSnapPoint(e, s);
        if (!i.snap) {
          this._hideSnapSquare();
          return;
        }
        i.distance < 5 ? (t.mousePosInScene = { x: i.snap.point.x, y: i.snap.point.y, z: i.snap.point.z }, await this._showSnapSquare(i)) : this._hideSnapSquare();
      }
    }
  }
  resetSnaps() {
    this._clearSnapSquare(), this.snaps = new yc(), this.snaps.process(this.__cache.meshes);
  }
  _findNearestSnapPoint(t, e) {
    let s = { snap: null, distance: 1 / 0 };
    for (let i = 0; i < e.snaps.length; i++) {
      const n = e.snaps[i];
      let r = n.point.distanceTo(t);
      r < s.distance && (s.snap = n, s.distance = r);
    }
    return s;
  }
  _hideSnapSquare() {
    this._snapSquare && (this._snapSquare.visible = !1);
  }
  async _showSnapSquare(t) {
    if (!this._snapSquare || this._changeSquareSize) {
      this._clearSnapSquare();
      let e = this._getSize();
      this._snapSquare = new gt(new Nr(e, e, e), new ks({ color: 16753920, wireframe: !0 })), this._snapSquare.visible = !1, this._snapSquare.initial = !0, this.scene.add(this._snapSquare);
    }
    this._snapSquare.position.copy(t.snap.point), this._snapSquare.visible = !0, await this.trigger("nearSnap", t);
  }
  _getSize() {
    const t = {
      width: this.camera.right / this.camera.zoom - this.camera.left / this.camera.zoom,
      height: this.camera.top / this.camera.zoom - this.camera.bottom / this.camera.zoom
    };
    return delete this._changeSquareSize, Math.max(t.width, t.height) / 100;
  }
  _getMousePosInScene(t) {
    let e = t.currentTarget, s = this.camera.position.distanceTo(this.controls.target);
    this.mouse.x = t.offsetX / e.offsetWidth * 2 - 1, this.mouse.y = -(t.offsetY / e.offsetHeight) * 2 + 1, this.planeNormal.copy(this.camera.getWorldDirection(this.vectorHelper));
    let i = this.camera.position.clone();
    return i.add(this.planeNormal.multiplyScalar(s)), this.plane.setFromNormalAndCoplanarPoint(this.planeNormal, i), this.raycaster.setFromCamera(this.mouse, this.camera), this.raycaster.ray.intersectPlane(this.plane, this.mousePos), this.mousePos;
  }
  /**
   * Clears all the data for a correct dispose of the object.
  */
  clear() {
    this._clearSnapSquare(), this.snaps.clear(), this.container.removeEventListener("pointerdown", this._mouseDownEvent), this.container.removeEventListener("pointerup", this._mouseUpEvent), this.container.removeEventListener("pointermove", this._mouseMoveEvent), this.snaps = null, this.container = null, this.scene = null, this.camera = null, this.controls = null, this.raycaster = null, this.mouse = null, this.plane = null, this.planeNormal = null, this.mousePos = null, this.vectorHelper = null;
  }
  _clearSnapSquare() {
    this._snapSquare && (this._snapSquare.parent && this._snapSquare.parent.remove(this._snapSquare), this._snapSquare.geometry.dispose(), this._snapSquare.material.dispose(), this._snapSquare = null);
  }
}
class mm {
  constructor(t, e, s) {
    this.camera = e, this.container = t, this.raycaster = new Fc(), this._calculateThreshold(), this.targets = s, this.container.addEventListener("wheel", () => {
      this._calculateThreshold();
    });
  }
  raycast(t) {
    this.raycaster.setFromCamera(t, this.camera);
    const e = this.raycaster.intersectObjects(this.targets, !0);
    return e.length === 0 ? null : e[0];
  }
  _calculateThreshold() {
    const t = {
      width: this.camera.right / this.camera.zoom - this.camera.left / this.camera.zoom,
      height: this.camera.top / this.camera.zoom - this.camera.bottom / this.camera.zoom
    }, e = Math.min(t.width, t.height);
    this.raycaster.params.Line.threshold = e / 500;
  }
}
class ua extends zs {
  constructor() {
    super();
  }
  _initRaycasting(t, e, s, i) {
    const n = [];
    s.traverse((r) => {
      r.geometry && n.push(r);
    }), this.pointer = new v(), this.raycast = i || new mm(t, e, n);
  }
  _clone(t) {
    const e = {};
    t.traverse((i) => {
      e[i.uuid] = i.userData, i.userData = null;
    });
    const s = t.clone();
    return t.traverse((i) => {
      i.userData = e[i.uuid];
    }), s;
  }
  _setMaterial(t) {
    const e = new ks({ depthTest: !1, depthWrite: !1 });
    return e.color.setHex(t), e.color.convertSRGBToLinear(), e;
  }
  _isInsideEntityList(t) {
    const e = t.userData.entity;
    return e ? this.dxf.entities.find((s) => s === e || s.block === e.name) : !1;
  }
}
class Sm extends ua {
  constructor(t, e, s, i, n = null) {
    super(), this.container = t, this._clonedObjects = {}, this.dxf3d = s, this.dxf = i, this._initRaycasting(t, e, s, n), this._material = this._setMaterial(16753920), this.container.addEventListener("pointermove", async (r) => await this._onPointerMove(r), !1);
  }
  async _onPointerMove(t) {
    t.preventDefault();
    let e = t.target.getBoundingClientRect();
    const s = t.clientX - e.left, i = t.clientY - e.top;
    this.pointer.x = s / this.container.clientWidth * 2 - 1, this.pointer.y = -(i / this.container.clientHeight) * 2 + 1;
    const n = this.raycast.raycast(this.pointer);
    if (this.removeHover(), n) {
      const r = this._isInsideEntityList(n.object) ? n.object : n.object.parent;
      if (!r.userData) return;
      this.hover(r), await this.trigger("hover", r);
    }
  }
  hover(t, e = null) {
    let s = null, i = t.parent;
    for (; s === null && i !== null; )
      i.name === "DIMENSION" && (s = i), i = i.parent;
    s && (t = s), this._clonedObjects[t.uuid] || (this._clonedObjects[t.uuid] = { clone: this._clone(t), parent: t.parent });
    const n = this._clonedObjects[t.uuid];
    n.clone.hovered = !0, n.clone.traverse((r) => {
      r.material && (r.material = e || this._material);
    }), this._hovered = n.clone, n.parent.add(this._hovered);
  }
  removeHover() {
    this._hovered && this._hovered.parent && (this._hovered.parent.remove(this._hovered), this._hovered = null);
  }
}
class Zm extends ua {
  constructor(t, e, s, i, n = null) {
    super(), this.container = t, this.camera = e, this.dxf3d = s, this.dxf = i, this._initRaycasting(t, e, s, n), this._material = this._setMaterial(255), this.selecteds = [], this._boxHelpers = {
      start3dpoint: new R(),
      end3dpoint: new R(),
      boxMin: new R(),
      boxMax: new R()
    }, this._isMouseDown = !1, this._isMouseMoving = !1, this.container.addEventListener("pointerdown", async (r) => await this._onPointerDown(r), !1), this.container.addEventListener("pointerup", async (r) => await this._onPointerUp(r), !1), this.container.addEventListener("pointermove", async (r) => await this._onPointerMove(r), !1);
  }
  async _onPointerDown(t) {
    if (t.button === 0 && (this._isMouseDown = !0, t.ctrlKey)) {
      let e = t.target.getBoundingClientRect();
      const s = t.clientX - e.left, i = t.clientY - e.top;
      this._onSelectionBox = {
        start: { x: s, y: i },
        end: { x: s, y: i }
      };
    }
  }
  async _onPointerUp(t) {
    if (this._isMouseDown = !1, this._isMouseMoving) {
      this._isMouseMoving = !1;
      return;
    }
    if (t.button !== 0) return;
    t.preventDefault(), this.deselectAll();
    let e = null;
    if (this._onSelectionBox) {
      const s = this._getEntitiesUnderSelectionBox(this._onSelectionBox.start, this._onSelectionBox.end);
      s && (e = s);
    } else {
      let s = t.target.getBoundingClientRect();
      const i = t.clientX - s.left, n = t.clientY - s.top;
      this.pointer.x = i / this.container.clientWidth * 2 - 1, this.pointer.y = -(n / this.container.clientHeight) * 2 + 1;
      const r = await this.raycast.raycast(this.pointer);
      r && (e = this._isInsideEntityList(r.object) ? r.object : r.object.parent);
    }
    e && (this.select(e), await this.trigger("select", e)), this._removeSelectionBox(), this._onSelectionBox = null;
  }
  async _onPointerMove(t) {
    if (this._onSelectionBox) {
      let e = t.target.getBoundingClientRect();
      const s = t.clientX - e.left, i = t.clientY - e.top;
      this._onSelectionBox.end = { x: s, y: i }, this.drawSelectionBox(this._onSelectionBox.start, this._onSelectionBox.end, e);
      return;
    }
    this._isMouseDown && (this._isMouseMoving = !0);
  }
  drawSelectionBox(t, e, s) {
    this._removeSelectionBox();
    const i = Math.max(t.x, e.x) - Math.min(t.x, e.x), n = Math.max(t.y, e.y) - Math.min(t.y, e.y);
    this._selectionBox = document.createElement("div"), this._selectionBox.style.position = "absolute", this._selectionBox.style.border = "1px solid white", this._selectionBox.style.left = Math.min(t.x, e.x) + s.left + "px", this._selectionBox.style.top = Math.min(t.y, e.y) + s.top + "px", this._selectionBox.style.width = i + "px", this._selectionBox.style.height = n + "px", this._selectionBox.style.pointerEvents = "none", this._selectionBox.style.background = "blue", this._selectionBox.style.opacity = 0.25, document.body.appendChild(this._selectionBox);
  }
  _removeSelectionBox() {
    this._selectionBox && (document.body.removeChild(this._selectionBox), this._selectionBox = null);
  }
  _getEntitiesUnderSelectionBox(t, e) {
    t.x = t.x / this.container.clientWidth * 2 - 1, t.y = -(t.y / this.container.clientHeight) * 2 + 1, e.x = e.x / this.container.clientWidth * 2 - 1, e.y = -(e.y / this.container.clientHeight) * 2 + 1, this._boxHelpers.start3dpoint = new R(t.x, t.y, 0).unproject(this.camera), this._boxHelpers.end3dpoint = new R(e.x, e.y, 0).unproject(this.camera), this._boxHelpers.boxMin.set(
      Math.min(this._boxHelpers.start3dpoint.x, this._boxHelpers.end3dpoint.x),
      Math.min(this._boxHelpers.start3dpoint.y, this._boxHelpers.end3dpoint.y),
      0
    ), this._boxHelpers.boxMax.set(
      Math.max(this._boxHelpers.start3dpoint.x, this._boxHelpers.end3dpoint.x),
      Math.max(this._boxHelpers.start3dpoint.y, this._boxHelpers.end3dpoint.y),
      0
    );
    const s = new St(this._boxHelpers.boxMin, this._boxHelpers.boxMax), i = this._getBlocksUnderBox(s, this.dxf3d);
    return i.length > 0 ? i : null;
  }
  _getBlocksUnderBox(t, e) {
    const s = [];
    if (!e || !e.children) return s;
    if (this._fitInsideBox(t, e)) {
      let i = [];
      return this._getInsideElements(e, i), s.push(...i), s;
    }
    for (let i = 0; i < e.children.length; i++) {
      const n = e.children[i], r = this._getBlocksUnderBox(t, n);
      for (let o = 0; o < r.length; o++) {
        let l = [];
        this._getInsideElements(r[o], l), l.length > 0 && s.push(...l);
      }
    }
    return s;
  }
  _getInsideElements(t, e) {
    if (this._isInsideEntityList(t)) {
      e.push(t);
      return;
    }
    if (!(!t.children || t.children.length === 0))
      for (let s = 0; s < t.children.length; s++)
        this._getInsideElements(t.children[s], e);
  }
  _fitInsideBox(t, e) {
    let s = new St().setFromObject(e);
    return t.containsBox(s);
  }
  select(t, e = null) {
    (t instanceof Array ? t : [t]).forEach((i) => {
      let n = null, r = i.parent;
      for (; n === null && r !== null; )
        r.name === "DIMENSION" && (n = r), r = r.parent;
      n && (i = n);
      const o = this._clone(i);
      o.selected = !0, o.traverse((l) => {
        l.material && (l.material = e || this._material);
      }), i.parent.add(o), this.selecteds.push(o);
    });
  }
  deselectAll() {
    this.selecteds && (this.selecteds.forEach((t) => t.parent.remove(t)), this.selecteds.length = 0);
  }
}
class Gm extends ua {
  constructor(t, e, s, i, n = null) {
    super(), this.container = t, this.camera = e, this.dxf3d = s, this.dxf = i, this._initRaycasting(t, this.camera, s, n), this._material = this._setMaterial(255), this._materialHover = this._setMaterial(16753920), this._clonedObjects = {}, this.selecteds = [], this._boxHelpers = {
      start3dpoint: new R(),
      end3dpoint: new R(),
      boxMin: new R(),
      boxMax: new R()
    }, this._isMouseDown = !1, this._isMouseMoving = !1, this.container.addEventListener("pointerdown", async (r) => await this._onPointerDown(r), !1), this.container.addEventListener("pointerup", async (r) => await this._onPointerUp(r), !1), this.container.addEventListener("pointermove", async (r) => await this._onPointerMove(r), !1);
  }
  async _onPointerDown(t) {
    if (t.button === 0 && (this._isMouseDown = !0, t.ctrlKey)) {
      let e = t.target.getBoundingClientRect();
      const s = t.clientX - e.left, i = t.clientY - e.top;
      this._onSelectionBox = {
        start: { x: s, y: i },
        end: { x: s, y: i }
      };
    }
  }
  async _onPointerUp(t) {
    if (this._isMouseDown = !1, this._isMouseMoving) {
      this._isMouseMoving = !1;
      return;
    }
    if (t.button !== 0) return;
    t.preventDefault(), this.deselectAll();
    let e = null;
    if (this._onSelectionBox) {
      const s = this._getEntitiesUnderSelectionBox(this._onSelectionBox.start, this._onSelectionBox.end);
      s && (e = s);
    } else {
      let s = t.target.getBoundingClientRect();
      const i = t.clientX - s.left, n = t.clientY - s.top;
      this.pointer.x = i / this.container.clientWidth * 2 - 1, this.pointer.y = -(n / this.container.clientHeight) * 2 + 1;
      const r = await this.raycast.raycast(this.pointer);
      r && (e = this._isInsideEntityList(r.object) ? r.object : r.object.parent);
    }
    e && (this.select(e), await this.trigger("select", e)), this._removeSelectionBox(), this._onSelectionBox = null;
  }
  async _onPointerMove(t) {
    t.preventDefault();
    const e = t.target.getBoundingClientRect(), s = t.clientX - e.left, i = t.clientY - e.top;
    if (this._onSelectionBox) {
      this._onSelectionBox.end = { x: s, y: i }, this.drawSelectionBox(this._onSelectionBox.start, this._onSelectionBox.end, e);
      return;
    }
    this._isMouseDown && (this._isMouseMoving = !0), this.pointer.x = s / this.container.clientWidth * 2 - 1, this.pointer.y = -(i / this.container.clientHeight) * 2 + 1;
    const n = this.raycast.raycast(this.pointer);
    if (this.removeHover(), n) {
      const r = this._isInsideEntityList(n.object) ? n.object : n.object.parent;
      if (!r.userData) return;
      this.hover(r), await this.trigger("hover", r);
    }
  }
  drawSelectionBox(t, e, s) {
    this._removeSelectionBox();
    const i = Math.max(t.x, e.x) - Math.min(t.x, e.x), n = Math.max(t.y, e.y) - Math.min(t.y, e.y);
    this._selectionBox = document.createElement("div"), this._selectionBox.style.position = "absolute", this._selectionBox.style.border = "1px solid white", this._selectionBox.style.left = Math.min(t.x, e.x) + s.left + "px", this._selectionBox.style.top = Math.min(t.y, e.y) + s.top + "px", this._selectionBox.style.width = i + "px", this._selectionBox.style.height = n + "px", this._selectionBox.style.pointerEvents = "none", this._selectionBox.style.background = "blue", this._selectionBox.style.opacity = 0.25, document.body.appendChild(this._selectionBox);
  }
  _removeSelectionBox() {
    this._selectionBox && (document.body.removeChild(this._selectionBox), this._selectionBox = null);
  }
  _getEntitiesUnderSelectionBox(t, e) {
    t.x = t.x / this.container.clientWidth * 2 - 1, t.y = -(t.y / this.container.clientHeight) * 2 + 1, e.x = e.x / this.container.clientWidth * 2 - 1, e.y = -(e.y / this.container.clientHeight) * 2 + 1, this._boxHelpers.start3dpoint = new R(t.x, t.y, 0).unproject(this.camera), this._boxHelpers.end3dpoint = new R(e.x, e.y, 0).unproject(this.camera), this._boxHelpers.boxMin.set(
      Math.min(this._boxHelpers.start3dpoint.x, this._boxHelpers.end3dpoint.x),
      Math.min(this._boxHelpers.start3dpoint.y, this._boxHelpers.end3dpoint.y),
      0
    ), this._boxHelpers.boxMax.set(
      Math.max(this._boxHelpers.start3dpoint.x, this._boxHelpers.end3dpoint.x),
      Math.max(this._boxHelpers.start3dpoint.y, this._boxHelpers.end3dpoint.y),
      0
    );
    const s = new St(this._boxHelpers.boxMin, this._boxHelpers.boxMax), i = this._getBlocksUnderBox(s, this.dxf3d);
    return i.length > 0 ? i : null;
  }
  _getBlocksUnderBox(t, e) {
    const s = [];
    if (!e || !e.children) return s;
    if (this._fitInsideBox(t, e)) {
      let i = [];
      return this._getInsideElements(e, i), s.push(...i), s;
    }
    for (let i = 0; i < e.children.length; i++) {
      const n = e.children[i], r = this._getBlocksUnderBox(t, n);
      for (let o = 0; o < r.length; o++) {
        let l = [];
        this._getInsideElements(r[o], l), l.length > 0 && s.push(...l);
      }
    }
    return s;
  }
  _fitInsideBox(t, e) {
    let s = new St().setFromObject(e);
    return t.containsBox(s);
  }
  _getInsideElements(t, e) {
    if (this._isInsideEntityList(t)) {
      e.push(t);
      return;
    }
    if (!(!t.children || t.children.length === 0))
      for (let s = 0; s < t.children.length; s++)
        this._getInsideElements(t.children[s], e);
  }
  select(t, e = null) {
    (t instanceof Array ? t : [t]).forEach((i) => {
      i = this._checkForDimension(i);
      const n = this._clone(i);
      n.selected = !0, n.traverse((r) => {
        r.material && (r.material = e || this._material);
      }), n.renderOrder = 1, i.parent.add(n), this.selecteds.push(n);
    });
  }
  deselectAll() {
    this.selecteds && (this.selecteds.forEach((t) => t.parent.remove(t)), this.selecteds.length = 0);
  }
  hover(t, e = null) {
    t = this._checkForDimension(t), this._clonedObjects[t.uuid] || (this._clonedObjects[t.uuid] = { clone: this._clone(t), parent: t.parent });
    const s = this._clonedObjects[t.uuid];
    s.clone.hovered = !0, s.clone.traverse((i) => {
      i.material && (i.material = e || this._materialHover);
    }), this._hovered = s.clone, s.parent.add(this._hovered);
  }
  removeHover() {
    this._hovered && this._hovered.parent && (this._hovered.parent.remove(this._hovered), this._hovered = null);
  }
  _checkForDimension(t) {
    let e = t;
    for (; e !== null; ) {
      if (e.name === "DIMENSION") return e;
      e = e.parent;
    }
    return t;
  }
}
const Vm = {
  Unitless: 0,
  Inches: 1,
  Feet: 2,
  Miles: 3,
  Millimeters: 4,
  Centimeters: 5,
  Meters: 6,
  Kilometers: 7,
  Microinches: 8,
  Mils: 9,
  Yards: 10,
  Angstroms: 11,
  Nanometers: 12,
  Microns: 13,
  Decimeters: 14,
  Decameters: 15,
  Hectometers: 16,
  Gigameters: 17,
  "Astronomical units": 18,
  "Light years": 19,
  Parsecs: 20,
  "US Survey Feet": 21,
  "US Survey Inch": 22,
  "US Survey Yard": 23,
  "US Survey Mile": 24
};
export {
  Gm as CADControls,
  gm as DXFViewer,
  Sm as Hover,
  xd as Merger,
  Zm as Select,
  Rm as SnapsHelper,
  Vm as UNITS
};
