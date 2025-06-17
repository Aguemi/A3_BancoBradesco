
package br.com.unicuritiba.bancocentral.controller;

import br.com.unicuritiba.bancocentral.model.Installment;
import br.com.unicuritiba.bancocentral.service.InstallmentService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/installments")
public class InstallmentController {

    private final InstallmentService service;

    public InstallmentController(InstallmentService service) {
        this.service = service;
    }

    @GetMapping
    public List<Installment> findAll() {
        return service.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Installment> findById(@PathVariable Long id) {
        return service.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public Installment create(@RequestBody Installment obj) {
        return service.save(obj);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Installment> update(@PathVariable Long id, @RequestBody Installment obj) {
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
