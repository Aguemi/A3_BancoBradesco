
package br.com.unicuritiba.bancocentral.controller;

import br.com.unicuritiba.bancocentral.model.Guarantor;
import br.com.unicuritiba.bancocentral.service.GuarantorService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/guarantors")
public class GuarantorController {

    private final GuarantorService service;

    public GuarantorController(GuarantorService service) {
        this.service = service;
    }

    @GetMapping
    public List<Guarantor> findAll() {
        return service.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Guarantor> findById(@PathVariable Long id) {
        return service.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public Guarantor create(@RequestBody Guarantor obj) {
        return service.save(obj);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Guarantor> update(@PathVariable Long id, @RequestBody Guarantor obj) {
        return service.findById(id)
                .map(record -> {
                    obj.setId(id);
                    return ResponseEntity.ok().body(service.save(obj));
                })
                .orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> delete(@PathVariable Long id) {
        return service.findById(id)
                .map(record -> {
                    service.delete(id);
                    return ResponseEntity.ok().<Void>build();
                })
                .orElse(ResponseEntity.notFound().build());
    }
}
